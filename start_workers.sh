#!/bin/bash

COUNT=10 QUEUE=default APP_INCLUDE=/var/www/demo/bootstrap.php php vendor/chrisboulton/php-resque/bin/resque >> /tmp/resque.log 2>&1