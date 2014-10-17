<?php
namespace Job\Work;

class TestJob extends AbstractJob
{
    /**
     * Set up something before perform, like establishing a database connection
     */
    public function setUp()
    {
    }

    /**
     * Do the work!
     */
    public function perform()
    {
        sleep(10);

        /**
         * These are the array arguments that you passed to the job
         * Resque will dynamically inject these into the job when the worker calls it
         * These arguments are stored in redis
         *
         * @var array
         */
        $args = $this->args;

        // Create a JSON string out of the input arguments
        $string = json_encode(array_merge(array('date' => date('Y-m-d h:i:s')), $args)).PHP_EOL;

        // Append our string to a file
        $fp = fopen('/tmp/testjob.txt', 'a+');
        fwrite($fp, $string);
        fclose($fp);
    }

    /**
     * Run after perform, like closing resources, freeing up connections, writing out logs etc...
     */
    public function tearDown()
    {
    }
}