# !/bin/bash

php artisan migrate:rollback
php artisan migrate
rm -r /app/storage/processes

echo "Reset complete"