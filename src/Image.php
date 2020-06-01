<?php

namespace Shikaemon\ImageManager;

use Shikaemon\ImageManager\Exceptions\UnSupportedMethodException;

/**
 * Class Image
 * @package Shikaemon\ImageManager
 * @method static Shikaemon\ImageManager\Image activate($imageId, $userId = null);
 * @method static Shikaemon\ImageManager\Image delete($imageId, $userId = null);
 * @method static Shikaemon\ImageManager\Image generate($imageId, $image_type);
 * @method static Shikaemon\ImageManager\Image register($inputFile, $userId = null);
 */
class Image
{
    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
        $command = $this->executeCommand($this, $name, $arguments);
        return $command->hasOutput() ? $command->getOutput() : $this;
    }

    public static function __callStatic($name, $arguments)
    {
        $self = new self();
        $command = $self->executeCommand($self, $name, $arguments);
        return $command->hasOutput() ? $command->getOutput() : $self;
    }

    private function executeCommand($image, $name, $arguments)
    {
        $commandName = $this->getCommandClassName($name);
        $command = new $commandName($arguments);
        $command->execute($image);

        return $command;
    }

    /**
     * @param $name
     * @return string
     * @throws UnSupportedMethodException
     */
    private function getCommandClassName($name)
    {
        $name = ucfirst($name);

        $classname = sprintf('\Shikaemon\ImageManager\Commands\%sCommand', ucfirst($name));

        if (class_exists($classname)) {
            return $classname;
        }

        throw new UnSupportedMethodException();
    }
}