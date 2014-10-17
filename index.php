<?php
/**
 * Resque backed by redis demo
 *
 * @author Samir
 * @email sameg14@gmail.com
 * @see    http://kamisama.me/2012/10/09/background-jobs-with-php-and-resque-part-1-introduction/
 */

require('bootstrap.php');

use Job\Scheduler\JobScheduler;

/** @var string $route What page are we on? */
$route = isset($_GET['route']) ? $_GET['route'] : 'homepage';

switch ($route) {

    case 'homepage':
        ?>
        <h3>Resque Demo</h3>
        <ul>
            <li>
                <a href="index.php?route=schedule&jobClass=TestJob">
                    Schedule Test Job
                </a>
                - Fire off a job that doesn't really do anything, but takes a while to complete
            </li>
            <li>
                <a href="index.php?route=schedule&jobClass=EmailJob">
                    Schedule Email Job
                </a>
                - Schedule a job, and sends out a large email
            </li>
            <li>
                <a href="index.php?route=schedule&jobClass=DataMinerJob">
                    Schedule Data Miner Job
                </a>
                - Spin up a job that scrapes all the missed connections from craigslist, and returns the data back to the caller via a DataBroker
            </li>
        </ul>
        <?php
        break;

    case 'schedule':

        $Scheduler = new JobScheduler();

        /** @var string $jobClass What job are we trying to run? */
        $jobClass = isset($_GET['job']) ? $_GET['job'] : 'TestJob';

        $namespacePrefix = 'Job\\Work\\';

        // Set a fully name spaced job class i.e. the job that will run asynchronously
        $Scheduler->setJobClass($namespacePrefix . $jobClass);

        // Adding a switch here to create custom data for each of these jobs
        if ($jobClass == 'TestJob') {

            $jobData = array('data' => 'nope', 'soup' => 'foo young');

        } elseif ($jobClass == 'EmailJob') {

            $jobData = array('email_address' => 'sameg14@gmail.com');

        } elseif ($jobClass == 'DataMinerJob') {

            $jobData = array('url' => 'http://austin.craigslist.org/search/mis');
        }

        // Set job data on the scheduler
        $Scheduler->setJobData($jobData);
        break;

    default:
        echo 'No Route defined!';
        exit;
}

showStatusForm();


// Create some data payload to send to the job, this is what gets serialized and put in redis
$jobArgs = array(
    'name' => 'Samir',
    'timestamp' => strtotime('now')
);
//
//$jobId = Resque::enqueue($queue = 'default', $jobClass, $jobArgs, $trackStatus = true);
//
//echo 'Your job was successfully scheduled. Your jobId: '.$jobId;



