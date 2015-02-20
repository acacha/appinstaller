<?php namespace BootstrapApp\Installer\Console;

use ZipArchive;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends \Symfony\Component\Console\Command\Command {

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('new')
				->setDescription('Create a new Bootstrap-app application.')
				->addArgument('type', InputArgument::REQUIRED)
				->addArgument('name', InputArgument::REQUIRED);
	}

	/**
	 * Execute the command.
	 *
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$type = $input->getArgument('type');

		$this->verifyApplicationDoesntExist(
			$directory = getcwd().'/'.$input->getArgument('name'),
			$output
		);

		$output->writeln('<info>Crafting application...</info>');

		$this->download($this->getURLByType($type),$zipFile = $this->makeFilename())
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);

		$output->writeln('<comment>Application ready! Build something amazing.</comment>');
	}

	/**
	 * Verify that the application does not already exist.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
	{
		if (is_dir($directory))
		{
			$output->writeln('<error>Application already exists!</error>');

			exit(1);
		}

		if (is_file($directory))
		{
			$output->writeln('<error>A file already exists with choosed app name!</error>');

			exit(1);
		}
	}

	/**
	 * Generate a random temporary filename.
	 *
	 * @return string
	 */
	protected function makeFilename()
	{
		return getcwd().'/bootstrap-app_'.md5(time().uniqid()).'.zip';
	}


	/**
	 * base URL by Type
	 *
	 * @param  string  $zipFile
	 * @return $this
	 */
	protected function getURLByType($type)
	{
		switch ($type) {
		    case "laravel":
		    	return 'http://acacha.org/boostrap-app-downloads/bootstrap-app-laravel.zip';
		        break;
		    case "codeigniter":
		    	return '';
		        break;
		    default:
		    	$output->writeln('<error>Unknown type!</error>');
		        die(-1);    
		} 
	}

	/**
	 * Download the temporary Zip to the given file.
	 *
	 * @param  string  $zipFile
	 * @return $this
	 */
	protected function download($url,$zipFile)
	{
		$response = \GuzzleHttp\get($url)->getBody();

		file_put_contents($zipFile, $response);

		return $this;
	}

	/**
	 * Extract the zip file into the given directory.
	 *
	 * @param  string  $zipFile
	 * @param  string  $directory
	 * @return $this
	 */
	protected function extract($zipFile, $directory)
	{
		$archive = new ZipArchive;

		$archive->open($zipFile);

		$archive->extractTo($directory);

		$archive->close();

		return $this;
	}

	/**
	 * Clean-up the Zip file.
	 *
	 * @param  string  $zipFile
	 * @return $this
	 */
	protected function cleanUp($zipFile)
	{
		@chmod($zipFile, 0777);

		@unlink($zipFile);

		return $this;
	}

}