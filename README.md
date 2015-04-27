### Austin-Redis-Meetup - Resque Demo

> Our discussion today will revolve around [resque](https://github.com/resque/resque), a [Redis](http://redis.io/) backed job queuing system. 

Running code asynchronously is a sure fire way to ensure that your web server(s) don't crash if bombarded with a sudden influx of traffic, and to make your application work faster by deferring tasks. 
When the amount of shoppers in a grocery store increase, the store doesn't hire more employees, the shoppers just get in line and wait till they are served.
In a traditional client server model, requests are processed and served in real time, some of which could be deferred or run in the background.

For instance: Lets say you wanted to build a simple checkout process for an online store. Here are some of the steps you are likely to take in your code.
- Insert into the ```order``` table, create a record, generate an ```orderId```
- Insert into the ```order_product``` table and create several records per item that was purchased
- Pass the order (cart) data to a template that is responsible for generating a receipt
- Show the receipt on the confirmation page
- Grab the receipt HTML and embed it in an email
- Send out the email to the user
- Make an API call to an internal API to record the sale in a legacy ERP system
- Decrease inventory in the ERP system making yet another API call

If you didn't have a way to run code in the background, you would be forced to send the email and make the API calls in real time. 
If you did, you could defer those tasks to run asynchronously, as they don't directly impact data that the user needs to complete her experience.


#### Scheduling a new Job
```php
$Scheduler = new JobScheduler();

/** @var string $job Full namespaced path of job class */
$job = 'Job\\Work\\TestJob';

// Set a fully name spaced job class i.e. the job that will run asynchronously
$Scheduler->setJobClass($job);

/** @var array $jobData Data to put in the queue, which will be available to the worker */
$jobData = array(
    'to_name' => 'Samir Patel',
    'to_address' => 'sameg14@gmail.com',
    'subject' => 'Google news here',
    'url' => 'https://news.google.com/'
);

// Set job data on the scheduler
$Scheduler->setJobData($jobData);

// Which queue do you want this job to run on
$Scheduler->setQueue($queueName = 'default');

// Schedule this job i.e. put it in the queue for a worker to consume
$jobId = $Scheduler->schedule();
```

#### Sample Worker
All your workers should be in the following namespace ```Job\Work```, and must implement the public method ```perform()```. Any code that is in ```perform()``` will get executed by resque. 

```php
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
```

#### Additional Resources
[Setup and Usage](http://kamisama.me/2012/10/09/background-jobs-with-php-and-resque-part-1-introduction/) - Step by step article on how to setup and use

[Github case study](http://highscalability.com/blog/2009/11/6/product-resque-githubs-distrubuted-job-queue.html) - Product: Resque - GitHub's Distrubuted Job Queue

[Resque web](https://github.com/resque/resque-web) - Monitor job status and job queues

[Resque Brain](http://technology.stitchfix.com/resque-brain/) - Alternative to monitoring
