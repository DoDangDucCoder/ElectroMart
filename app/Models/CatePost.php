<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatePost extends Model
{
    protected $table = 'tbl_category_post';
    
    protected $primaryKey = 'cate_post_id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'cate_post_name',
        'cate_post_slug', 
        'cate_post_desc',
        'cate_post_status'
    ];
}
