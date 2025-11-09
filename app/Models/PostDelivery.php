<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDelivery extends Model {
  protected $fillable=['post_id','user_id','sent_at','status','last_error'];
}
