<?php

namespace Job\Work;

use Job\Util\DataBroker;

/**
 * Class AbstractJob
 *
 * @package Job\Work
 */
abstract class AbstractJob
{
    /**
     * Array of arguments injected into this job
     *
     * @var array
     */
    public $args;

    /**
     * Data broker is used to federate data from job to initiator
     *
     * @var DataBroker
     */
    protected $DataBroker;

    /**
     * Runs before the job starts executing
     */
    public function setUp()
    {
        $this->DataBroker = new DataBroker($this->getJobId());
    }

    /**
     * All children must implement
     *
     * @return void
     */
    abstract public function perform();

    /**
     * Run after perform, like closing resources, freeing up connections, writing out logs etc...
     */
    public function tearDown()
    {
        // Save values that our job could have generated
        $this->DataBroker->save();
    }

    /**
     * Get the jobId for the currently running job
     *
     * @return string
     */
    protected function getJobId()
    {
        return $this->job->payload['id'];
    }
}