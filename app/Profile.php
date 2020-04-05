<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    public function profileImage() {
      $imagePath = ($this->image) ?  $this->image :  '/profile/08Edz4s6kuMXJSPN0dnC0I9lH1RdbhixSbUp3F0X.png';
      return  '/storage/' . $imagePath;
    }

    // profiles has many followers
    public function followers() {
      return $this->belongsToMany(User::class);
    }

    public function user() {
      return $this->belongsTo(User::class);
    }

  
}
