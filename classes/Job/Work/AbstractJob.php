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
     * All children must implement
     * @return void
     */
    abstract public function perform();
}