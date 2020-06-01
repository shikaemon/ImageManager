<?php

namespace Shikaemon\ImageManager\Commands;

use Shikaemon\ImageManager\Image;

abstract class AbstractCommand
{
    /**
     * Arguments of command
     *
     * @var array
     */
    public $arguments;

    /**
     * Output of command
     *
     * @var mixed
     */
    protected $output;

    abstract public function execute(Image $image);


    /**
     * Creates new command instance
     *
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param $index
     * @return mixed|AbstractCommand
     */
    public function argument($index)
    {
        return $this->arguments[$index] ? $this->arguments[$index] : null;
    }
    /**
     * Returns output data of current command
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output ? $this->output : null;
    }

    /**
     * Determines if current instance has output data
     *
     * @return boolean
     */
    public function hasOutput()
    {
        return ! is_null($this->output);
    }

    /**
     * Sets output data of current command
     *
     * @param mixed $value
     */
    public function setOutput($value)
    {
        $this->output = $value;
    }
}