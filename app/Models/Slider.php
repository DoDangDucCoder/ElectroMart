<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
   public $timestamps = false; //set time to false
   protected $fillable = [
      'slider_name',
      'slider_alt',
      'slider_image',
      'slider_status',
      'slider_desc',
      'slider_tone',
   ];
   protected $primaryKey = 'slider_id';
   protected $table = 'tbl_slider';
}
