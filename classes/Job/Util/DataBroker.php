<?php
namespace Job\Util;

use Predis\Client;
use \Exception as Exception;

/**
 * Class DataBroker used to federate data
 * between an asynchronous process and the client that requested the job
 *
 * @package Job\Util
 */
class DataBroker
{
    /**
     * Redis client
     *
     * @var Client
     */
    protected $Client;

    /**
     * Unique job identifier
     *
     * @var string
     */
    protected $jobId;

    /**
     * Data that the job will set to redis
     *
     * @var mixed
     */
    protected $data;

    /**
     * @param string $jobId Required unique job identifier
     */
    public function __construct($jobId)
    {
        $this->Client = new Client();
        $this->jobId = $jobId;
    }

    /**
     * Get data that the worker generated
     *
     * @return mixed
     */
    public function get()
    {
        $data = $this->Client->get($this->getKey());
        return json_decode($data);
    }

    /**
     * Worker will use this method to set data for this jobId
     *
     * @throws Exception
     * @return bool
     */
    public function save()
    {
        //If we have no data to save, lets not
        if (empty($this->data)) {
            return false;
        }

        // If the data we are trying to set is an array
        if (is_array($this->data)) {

            //Convert it to JSON
            $dataToSet = json_encode($this->data);
        } else {

            // if the data is not an array, then put it in an array, then convert to JSON
            $dataToSet = json_encode(array($this->data));
        }

        /**
         * This key is simply the jobId, prefixed with 'job-data'
         * @var string
         */
        $key = $this->getKey();

        return $this->Client->set($key, $dataToSet);
    }

    /**
     * Get a namespaced key for worker data
     *
     * @return string
     */
    protected function getKey()
    {
        return 'job-data-' . $this->jobId;
    }

    /**
     * @param mixed $data Data that the async process with set
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}