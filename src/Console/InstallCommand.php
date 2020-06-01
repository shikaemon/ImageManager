<?php

namespace Shikaemon\ImageManager\Console;

use Illuminate\Console\Command;

/**
 * Class InstallCommand
 * @package Shikaemon\ImageManager\Console
 * @property string $signature
 * @property string $description
 * @property string $directory
 * @property \Illuminate\Contracts\Foundation\Application laravel
 */
class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'shikaemon_image:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the shikaemon image package';

    private $directory;

    public function handle()
    {
        $this->initDir();
        if (is_dir($this->directory)) {
            $this->line("<error>{$this->directory} directory already exists !</error> ");

            return;
        }
        // make base dir.
        $this->makeDir('');
        // make controller dir.
        $this->makeDir('Controllers');

        $this->makeImageController();
        $this->createRoutesFile();
    }

    private function migrate()
    {

    }

    private function initDir()
    {
        $this->directory = app_path(ucfirst(config('image_manager.directory')));
    }

    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }

    private function makeImageController()
    {
        $imageController = $this->directory.'/Controllers/ImageController.php';
        $contents = $this->getStub('ImageController');

        $this->laravel['files']->put(
            $imageController,
            str_replace('DummyNamespace', config('image_manager.route.namespace'), $contents)
        );

        $this->line('<info>ImageController file was created:</info> '.str_replace(base_path(), '', $imageController));
    }

    private function makeSampleController()
    {
        $imageController = $this->directory.'/Controllers/SampleController.php';
        $contents = $this->getStub('SampleController');

        $this->laravel['files']->put(
            $imageController,
            str_replace('DummyNamespace', config('image_manager.route.namespace'), $contents)
        );

        $this->line('<info>SampleController file was created:</info> '.str_replace(base_path(), '', $imageController));
    }

    private function createRoutes()
    {

    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file = $this->directory.'/routes.php';

        $contents = $this->getStub('routes');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Routes file was created:</info> '.str_replace(base_path(), '', $file));
    }

    private function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    private function createControllers()
    {

    }
}