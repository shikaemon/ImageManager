<?php

namespace Shikaemon\ImageManager\Libraries\Models\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageModel extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'images';

    const ID = 'id';
    const USER_ID = 'user_id';
    const IMAGE_KEY = 'image_key';
    const EXTENSION = 'extension';
    const FILE_PATH = 'file_path';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const LONGITUDE = 'longitude';
    const LATITUDE = 'latitude';
    const POSTED_ON = 'posted_on';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    const STATUS_ACTIVATED = 1;
    const STATUS_REGISTERED = 99;

    protected $dates = [self::POSTED_ON, self::DELETED_AT];

    protected $fillable = [
        self::ID,
        self::USER_ID,
        self::IMAGE_KEY,
        self::EXTENSION,
        self::FILE_PATH,
        self::WIDTH,
        self::HEIGHT,
        self::LONGITUDE,
        self::LATITUDE,
        self::POSTED_ON,
    ];

    protected $table = self::TABLE_NAME;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->status = self::STATUS_REGISTERED;
    }
}