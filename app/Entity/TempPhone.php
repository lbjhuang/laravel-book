<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class TempPhone extends Model
{
    protected $table = 'temp_phone';
    protected $primaryKey = 'id';

    public $timestamps = false;  //不写入自带的两个时间参数
}
