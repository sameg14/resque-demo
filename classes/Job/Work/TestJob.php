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
     * File pointer
     *
     * @var resource
     */
    protected $fp;

    /**
     * Set up something before perform, like establishing a database connection
     */
    public function setUp()
    {
        $this->fp = fopen('/tmp/testjob.txt', 'a+');
    }

    /**
     * Do the work!
     */
    public function perform()
    {
        // Add some randomness to simulate actual work
        sleep(rand(1, 3));

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
        $string .= '--------------------------------------------------------------------------------'.PHP_EOL;

        // Append our string to the test file
        fwrite($this->fp, $string);
    }

    /**
     * Run after perform, like closing resources, freeing up connections, writing out logs etc...
     */
    public function tearDown()
    {
        fclose($this->fp);
    }
}