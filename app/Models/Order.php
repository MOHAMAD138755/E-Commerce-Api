<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'id','user_id','receiver_name','address','phone_number','status','total_price'
    ];
}
