wget https://github.com/bootstrap-app/laravel/archive/master.zip
unzip master.zip -d working
cd working/laravel-master
composer install
zip -r ../../laravel-craft.zip .
cd ../..
mv laravel-craft.zip public/bootstrap-app-laravel.zip
rm -rf working
rm master.zip
