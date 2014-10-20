<?php

namespace Job\Scheduler;

use \Resque as Resque;
use \Resque_Job_Status as Resque_Job_Status;

/**
 * Responsible for scheduling jobs
 * Class JobScheduler
 *
 * @package Job\Scheduler
 */
class JobScheduler
{
    /**
     * Name of queue
     *
     * @var string
     */
    protected $queue = 'default';

    /**
     * Fully namespaced job class
     *
     * @var string
     */
    protected $jobClass;

    /**
     * Array of any data you want to send to the job
     *
     * @var array
     */
    protected $jobData = array();

    /**
     * JobId from a newly created job
     *
     * @var int
     */
    protected $jobId;


    public function __construct()
    {
        // Tell resque where redis lives and what port its on
        Resque::setBackend(REDIS_SERVER_IP . ':' . REDIS_SERVER_PORT);
    }

    /**
     * Schedule a job i.e. add it to the redis backed queue
     *
     * @return int jobId from the newly created job
     */
    public function schedule()
    {
        $this->jobId = Resque::enqueue($this->queue, $this->jobClass, $this->jobData, $trackStatus = true);
        return $this->jobId;
    }

    /**
     * Get status of a job.
     *
     * @return string
     */
    public function getStatus()
    {
        $Job = new Resque_Job_Status($this->jobId);
        $jobStatusCode =  $Job->get();

        switch($jobStatusCode){

            case Resque_Job_Status::STATUS_WAITING:
                return 'Waiting';
            break;

            case Resque_Job_Status::STATUS_COMPLETE:
                return 'Complete';
            break;

            case Resque_Job_Status::STATUS_FAILED:
                return "Failed";
            break;

            case Resque_Job_Status::STATUS_RUNNING:
                return 'Running';
            break;

            default:
                return 'Unknown! Code: '.$jobStatusCode;
        }
    }

    /**
     * Get a list of all queues from redis.
     *
     * @return array Array of queues.
     */
    public function getQueues()
    {
        return Resque::queues();
    }

    /**
     * How many jobs are currently in a queue?
     *
     * @return int how many jobs are in the queue
     */
    public function size()
    {
        return Resque::size($this->queue);
    }

    /**
     * @return mixed
     */
    public function getWorkers()
    {
        return \Resque_Worker::all();
    }

    /**
     * @param string $jobClass
     */
    public function setJobClass($jobClass)
    {
        $this->jobClass = $jobClass;
    }

    /**
     * @return string
     */
    public function getJobClass()
    {
        return $this->jobClass;
    }

    /**
     * @param array $jobData
     */
    public function setJobData($jobData)
    {
        $this->jobData = $jobData;
    }

    /**
     * @return array
     */
    public function getJobData()
    {
        return $this->jobData;
    }

    /**
     * @param int $jobId
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * @return int
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @param string $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }
}