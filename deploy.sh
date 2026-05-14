#===================== implement backend  =======================
echo "start deploy 2b website \n"

#!/bin/bash
if [ -f .git/index.lock ]; then
    echo "Lock file exists, removing it..."
    rm .git/index.lock
fi
#pull
git pull origin main

# update PHP dependencies
composer install --ignore-platform-reqs
# --no-interaction Do not ask any interactive question

php artisan migrate --force

php artisan db:seed --force

php artisan optimize:clear

echo "Deployed Successfully............ \n"
