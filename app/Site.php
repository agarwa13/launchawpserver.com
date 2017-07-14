<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Site
 * @property $id
 * @property $forge_site_id
 * @property $domain_name
 * @property $forge_database_id
 * @property $forge_database_user_id
 * @property $database_name
 * @property $database_user_name
 * @property $database_user_password
 * @property $server_id
 * @property $created_at
 * @property $updated_at
 * @property $server
 * @property $status
 * @package App
 */
class Site extends Model
{
    /**
     * Get the Server that this Site belongs To
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server(){
        return $this->belongsTo('App\Server');
    }
}
