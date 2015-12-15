#!/bin/bash
COUNT=10 QUEUE=default APP_INCLUDE=/var/www/resque-demo/bootstrap.php php vendor/chrisboulton/php-resque/bin/resque >> /var/log/resque.log 2>&1