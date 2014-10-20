<?php
namespace Job\Work;

/**
 * Class TestJob - Test resque job
 *
 * @package Job\Work
 */
class TestJob extends AbstractJob
{
    /**
     * Do the work!
     */
    public function perform()
    {
        // Add some randomness to simulate actual work
        sleep(rand(3, 10));

        /**
         * These are the array arguments that you passed to the job
         * Resque will dynamically inject these into the job when the worker calls it
         * These arguments are stored in redis
         *
         * @var array
         */
        $args = $this->args;

        // Create a JSON string out of the input arguments
        $string = 'YOUR PAYLOAD::' . PHP_EOL;
        $string .= json_encode(array_merge(array('date' => date('Y-m-d h:i:s')), $args)) . PHP_EOL;
        $string .= 'YOUR JOB ID:: ';
        $string .= $this->getJobId() . PHP_EOL;
        $string .= '--------------------------------------------------------------------------------' . PHP_EOL;

        $fp = fopen('/tmp/testjob.txt', 'a+');

        // Append our string to the test file
        fwrite($fp, $string);
        fclose($fp);

        // If you want to send data back to the client, you can use the DataBroker
        $this->DataBroker->setData('This the data that comes back from your job!');
    }
}