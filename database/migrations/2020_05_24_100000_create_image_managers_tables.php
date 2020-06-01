<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageManagersTables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('image_manager.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('image_manager.database.images_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User\'s id. It should be linked id of users table.');
            $table->string('image_key', 190)->comment('hash for image idntifier.');
            $table->string('extension', 10)->comment('image extensions. eg:jpg,jpeg,gif,png,heic...');
            $table->decimal('width', 10, 4)->nullable()->comment('image width');
            $table->decimal('height', 10, 4)->nullable()->comment('image height.');
            $table->string('file_path', 255)->comment('file pat. eg:/2020/05/04/image_key.extension');
            $table->dateTime('image_at')->nullable()->comment('datetime of image created at. It\'s used by image file path');
            $table->tinyInteger('status')->nullable()->comment('Publishing Setting. 1=published, 99=unpublished');
            $table->softDeletes();
            if (config('image_manager.database.can_make_foreign_key_to_users_table_id')) {
                $table->foreign('user_id')->references('id')->on('users');
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('image_manager.database.images_table'));
    }
}
