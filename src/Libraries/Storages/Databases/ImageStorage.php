<?php

namespace Shikaemon\ImageManager\Libraries\Storages\Databases;

use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;

class ImageStorage
{
    /**
     * @param $id
     * @return ImageModel | null
     */
    public function retrieveById($id)
    {
        return ImageModel::query()->where(ImageModel::ID, $id)->get()->first();
    }

    /**
     * @param $id
     * @param $userId
     * @return ImageModel | null
     */
    public function retrieveByIdAndUserId($id, $userId)
    {
        $query = ImageModel::query();
        $query->where(ImageModel::ID, $id);
        if (isset($userId)) {
            $query->where(ImageModel::USER_ID, $userId);
        }

        return $query->get()->first();
    }
}