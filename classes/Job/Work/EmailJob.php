<?php

namespace Job\Work;

/**
 * Class EmailJob send out emails
 *
 * @package Job\Work
 */
class EmailJob extends AbstractJob
{
    /**
     * Send out an email containing the contents of the URL
     */
    public function perform()
    {
        // Add some randomness to simulate actual work
        sleep(rand(3, 10));

        // Grab the contents of whatever url is specified
        $message = file_get_contents($this->args['url']);

        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'To: ' . $this->args['to_name'] . ' <' . $this->args['to_address'] . '>' . "\r\n";
        $headers .= 'From: Resque Data <resque@isawesome.com>' . "\r\n";

        // Mail it
        mail($this->args['to_name'], $this->args['subject'], $message, $headers);

        $this->DataBroker->setData('Check your inbox!');
    }
}