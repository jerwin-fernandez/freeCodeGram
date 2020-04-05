<?php

namespace App;

use App\Mail\NewUserWelcomeMail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public static function boot() {
      parent::boot();

      // event. will executed if a new user is created, then create a partial profile for it too.
      static::created(function ($user) {
        $user->profile()->create([
          'title' => $user->username, // default value for its title
        ]);
        
        Mail::to($user->email)->send(new NewUserWelcomeMail);

      });
    }

    // users has many following
    public function following() {
      return $this->belongsToMany(Profile::class);
    }

    // profile because hasOne so it is single
    public function profile() {
      return $this->hasOne(Profile::class);
    }

    // here posts plural, because of hasMany relationship
    public function posts() {
      return $this->hasMany(Post::class)->orderBy('created_at', 'DESC');
    }
}
