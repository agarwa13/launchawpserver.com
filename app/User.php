<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @property $id
 * @property $name
 * @property $email
 * @property $password
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 * @property $credentials
 * @property $servers
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $with = ['credentials.servers.sites'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
     * Get the Credentials for this User
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credentials()
    {
        return $this->hasMany('App\Credential');
    }

    /**
     * Get the Servers for this User
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function servers()
    {
        return $this->hasManyThrough('App\Server','App\Credential');
    }

}
