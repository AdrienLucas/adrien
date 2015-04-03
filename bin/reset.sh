#!/bin/bash

php app/console do:da:dr --force
php app/console do:da:cr
php app/console do:sc:up --force
php app/console do:fi:lo
php app/console ca:cl

