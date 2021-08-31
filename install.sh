chown -R www-data:www-data /var/www
chmod -R 755 /var/www/vhosts/ws/storage

php artisan migrate --seed
php artisan migrate:client demo
php artisan migrate:client nsp