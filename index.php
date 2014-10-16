<?php
//require('php-resque/lib/Resque.php');
//
//// Tell resque where redis lives, in this demo it is local
//Resque::setBackend('127.0.0.1:6379');
//
//// Create some data payload to send to the job, this is what gets serialized and put in redis
//$jobArgs = array(
//    'name' => 'Samir',
//    'timestamp' => strtotime('now')
//);
//
////$jobId = Resque::enqueue('default',
