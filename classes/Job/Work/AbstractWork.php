<?php

namespace Job\Work;

/**
 * Class AbstractWork
 *
 * @package Job\Work
 */
abstract class AbstractWork
{
    /**
     * All children must implement
     * @return void
     */
    abstract public function perform();


}