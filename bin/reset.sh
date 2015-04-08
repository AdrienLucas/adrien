#!/bin/bash

php app/console do:sc:dr --force --full-database
php app/console do:sc:up --force
php app/console do:fi:lo
php app/console ca:cl

