<?php

namespace Job\Work;

/**
 * Class DataMinerJob get all links and descriptions from craigslist missed connections.
 *
 * @package Job\Work
 */
class DataMinerJob extends AbstractJob
{
    public function perform()
    {
        sleep(6);

        /** @var string $return Formatted data string we get back from job */
        $return = '<ul>';

        // Suppress markup warnings
        libxml_use_internal_errors(false);

        $html = file_get_contents($this->args['url']);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $nodeList = $xpath->query('//a[@class="hdrlnk"]');
        foreach ($nodeList as $node) {

            $href = $node->getAttribute('href');
            $text = $node->textContent;

            $url = 'http://austin.craigslist.org'.$href;

            $return .= '<li>'.$text.' - <a href="'.$url.'" target="_blank">'.$url.'</a></li>';
        }

        $return .= '</ul>';

        $this->DataBroker->setData($return);
    }
}