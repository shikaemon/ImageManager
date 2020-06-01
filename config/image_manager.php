<?php
return [
    'name' => 'ImageManager',

    'route_file' => 'routes/web.php',

    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // if you changed data type of users.id from increment to BigIncrement, You may set true.
        'can_make_foreign_key_to_users_table_id' => true,

        // Images tables and model.
        'images_table' => 'images',
        'images_model' => Shikaemon\ImageManager\Libraries\Model\Database\Images::class,
    ],

    'filesystems' => [
        's3' => [
            'public' => 's3',
            'temp' => 's3temp',
            'original' => 's3original',
        ],
    ],

    'route' => [

        'prefix' => env('IMAGE_ROUTE_PREFIX', 'image'),

        'namespace' => 'App\\Image\\Controllers',

        'middleware' => ['web'],
    ],

    'auth' => [

        'controller' => App\Admin\Controllers\AuthController::class,

        'guard' => 'admin',

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => '   eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,

        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => 'auth/login',

        // The URIs that should be excluded from authorization.
        'excepts' => [
            'auth/login',
            'auth/logout',
            '_handle_action_',
        ],
    ],

    'directory' => 'Image',

    /**
     *
     */
    'image_types' => [
        'thumbnail' => [
            'width' => 250,
            'height' => 250,
            'resize' => 'cover'
        ],
        'profile' => [
            'width' => 150,
            'height' => 150,
            'resize' => 'cover'
        ],
    ],
];
