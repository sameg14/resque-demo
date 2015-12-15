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
    {
        ?>
        <style>
            body {
                font-family: verdana, arial, sans-serif;
                background-color: #eee;
                padding: 50px;
            }
        </style>
        <?php include('github_ribbon.htm'); ?>
        <h3>Resque Demo</h3>
        <hr style="color:#D01F3C;"/>
        <ul>
            <li>
                <a href="index.php?route=schedule&jobClass=TestJob">
                    Schedule Test Job
                </a>
                - Fire off a job that doesn't really do anything interesting, but takes a while to complete
            </li>
            <li>
                <a href="index.php?route=schedule&jobClass=EmailJob">
                    Schedule Email Job
                </a>
                - Schedule a job that grabs HTML from a page and sends it out via email
            </li>
            <li>
                <a href="index.php?route=schedule&jobClass=DataMinerJob">
                    Schedule Data Miner Job
                </a>
                - Create a job that scrapes Craigslist missed connections. Return the data back to the client via a redis backed DataBroker
            </li>
            <li>
                <a href="<?php echo($_SERVER['HTTP_HOST']);?>:5678">View running jobs using resque-web interface</a>
            </li>
        </ul>
        <?php
        break;
    }

    case 'schedule':
    {
        $Scheduler = new JobScheduler();

        /** @var string $jobClass What job are we trying to run? */
        $jobClass = isset($_GET['jobClass']) ? $_GET['jobClass'] : 'TestJob';

        $namespacePrefix = 'Job\\Work\\';

        // Set a fully name spaced job class i.e. the job that will run asynchronously
        $Scheduler->setJobClass($namespacePrefix . $jobClass);

        // Adding a switch here to create custom data for each of these jobs
        if ($jobClass == 'TestJob') {

            $jobData = array('data' => 'no', 'soup' => 'foo young');

        } elseif ($jobClass == 'EmailJob') {

            $jobData = array(
                'to_name' => 'Samir Patel',
                'to_address' => 'sameg14@gmail.com',
                'subject' => 'Google news here',
                'url' => 'https://news.google.com/'
            );

        } elseif ($jobClass == 'DataMinerJob') {

            $jobData = array('url' => 'http://austin.craigslist.org/search/mis');
        }

        // Set job data on the scheduler
        $Scheduler->setJobData($jobData);

        // Which queue do you want this job to run on
        $Scheduler->setQueue($queueName = 'default');

        // Schedule this job i.e. put it in the queue for a worker to consumer
        $jobId = $Scheduler->schedule();
        echo '
        <style>
            body {
                font-family: verdana, arial, sans-serif;
                background-color: #eee;
                padding: 50px;
            }
        </style>';
        echo 'Your job "<b>' . $jobClass . '</b>" was successfully scheduled. JobId: <b>' . $jobId . '</b>';

        break;
    }

    case 'status':
    {
        $jsonResponse = array();

        $jobId = $_REQUEST['jobId'];

        $jsonResponse['jobId'] = $jobId;

        $Scheduler = new JobScheduler();
        $Scheduler->setJobId($jobId);
        $status = $Scheduler->getStatus();
        $jsonResponse['data'] = null;
        $jsonResponse['status'] = $status;

        // Job is complete, try to retrieve any data that was set by job.
        if ($status == 'Complete') {
            $returnedData = $Scheduler->getReturnedData();
            $jsonResponse['data'] = $returnedData;
        }
        echo json_encode($jsonResponse);
        exit;
    }
}

// Poll the /status endpoint for the status of the job
if (isset($jobId) && !empty($jobId)) {
    echo '<h4>Checking Job Status</h4>';
    ?>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" language="javascript">

        var numChecks = 0;
        var shouldStopPolling = false;

        var interval = setInterval(function () {

            // When the job is complete, stop polling the server
            if (shouldStopPolling == true) {
                clearInterval(interval);
            }

            // Make an AJAX call to the server to check status, and get data, if applicable.
            $.ajax({
                url: "/index.php",
                data: {
                    route: 'status',
                    jobId: "<?php echo($jobId);?>"
                },
                dataType: "json",
                success: function (jsonData) {

                    shouldStopPolling = jsonData['status'] == 'Complete' ? true : false;

                    var writeString = 'Status = <b>' + jsonData['status'] + '</b>     Data = <b>' + jsonData['data'] + '</b>';

                    // Append the status message from the server to this Div.
                    $("#div-job-status").append('', numChecks + ') ').append('', writeString).append('', '<br/>');
                }
            });

            ++numChecks;

        }, 3000);
    </script>
    <div id="div-job-status"></div>
<?php
}
?>