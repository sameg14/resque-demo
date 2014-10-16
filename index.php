<?php
// Excellent blog post: http://kamisama.me/2012/10/09/background-jobs-with-php-and-resque-part-1-introduction/

// Include composer's auto loader
require('bootstrap.php');

use Job\Scheduler\JobScheduler;

//// Get the job name from the URL, if not set, then default it to something
//$jobClass = isset($_GET['job']) ? $_GET['job'] : 'TestJob';
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
//$jobId = Resque::enqueue($queue = 'default', $jobClass, $jobArgs, $trackStatus = true);
//
//echo 'Your job was successfully scheduled. Your jobId: '.$jobId;

$Scheduler = new JobScheduler();

