<?php

namespace Job\Work;

/**
 * Class AbstractJob
 *
 * @package Job\Work
 */
abstract class AbstractJob
{
    /**
     * Array of arguments injected into this job
     * @var array
     */
    public $args;

    /**
     * All children must implement
     *
     * @return void
     */
    abstract public function perform();

    /**
     * Get the jobId for the currently running job
     * @return string
     */
    protected function getJobId()
    {
        return $this->job->payload['id'];
    }
}