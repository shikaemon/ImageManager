<?php

namespace Shikaemon\ImageManager;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Shikaemon\ImageManager\Libraries\Repositories\Interfaces\ImageInterface;
use Shikaemon\ImageManager\Libraries\Repositories\ImageRepository;
use Shikaemon\ImageManager\Libraries\Services\ImageService;

class ImageManagerServiceProvider extends ServiceProvider
{
    protected $commands = [
        Console\InstallCommand::class,
    ];
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publish();
        // routes.phpが所定の領域にあればroutes.phpを読み込む
        $this->directory = app_path(ucfirst(config('image_manager.directory')));

        if (file_exists($routes = root_path('routes.php'))) {
            // routesファイルを読み込む
            $this->loadRoutesFrom($routes);
        }
        // 画像再生性用のendpointを作成し、そこにリダイレクトされてきたら画像処理をして、S3にアップロードと画像をそのまま出力。
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ImageInterface::class, ImageRepository::class);
        $this->app->bind(ImageService::class, ImageService::class);
        $this->loadConfig();
        $this->registerRouteMiddleware();
        // コマンドの登録。
        $this->commands($this->commands);

        $this->app->bind('Shikaemon\ImageManager\Libraries\Repositories\Interfaces\ImageInterface', 'Shikaemon\ImageManager\Libraries\Repositories\ImageRepository');
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }
    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadConfig()
    {
        config('image_manager.auth');
    }

    private function publish()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'image-manager-configs');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'image-manager-migrations');
        }
    }
}