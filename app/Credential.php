<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Credential
 * @property $id
 * @property $name
 * @property $access_key_id
 * @property $secret_access_key
 * @property $user_id
 * @property $created_at
 * @property $updated_at
 * @property $user
 * @property $servers
 * @package App
 */
class Credential extends Model
{
    /**
     * Get the User that this Credential belongs To
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * Get the Servers created using this Credential
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function servers(){
        return $this->hasMany('App\Server');
    }
}
