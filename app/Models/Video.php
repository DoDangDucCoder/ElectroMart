<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Video extends Model
{
    public $timestamps = false; //set time to false
    protected $fillable = [
        'video_title',
        'video_link',
        'video_desc',
        'video_slug',
        'video_image'
    ];
    protected $primaryKey = 'video_id';
    protected $table = 'tbl_videos';

    public function getTable()
    {
        $table = $this->table;

        if (Schema::hasTable($table)) {
            return $table;
        }

        if (Schema::hasTable('tbl_video')) {
            return 'tbl_video';
        }

        return $table;
    }
}
