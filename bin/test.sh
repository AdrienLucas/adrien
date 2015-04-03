#!/bin/bash

rm -rf /var/www/poledev/app/cache/t*

phpunit -c /var/www/poledev/app
