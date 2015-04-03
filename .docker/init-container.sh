#!/bin/sh

echo -e "\033[32m\033[1;37mContainer Initialization\033[0m"
rm -rf /var/www/cache/*
rm -rf /var/www/log/*

echo -e "\033[32m\033[1;37mMysql Launch\033[0m"
service mysql start

echo -e "\033[32m\033[1;37mNginx Launch\033[0m"
service nginx start

echo -e "\033[32m\033[1;37mPHP5-FPM Launch\033[0m"
service php5-fpm start

echo -e "\033[32m\033[1;37mBoostraping the app\033[0m"
cd /var/www/poledev
sh ./bin/reset.sh
chown -R www-data:www-data ./

echo -e "\033[32m\033[1;37mReading /var/log/faillog\033[0m"
tail -f /var/log/faillog
