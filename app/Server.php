<?php

namespace App;

use App\Events\ServerStatusUpdated;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Server
 * @property $id
 * @property $name
 * @property $size
 * @property $region
 * @property $php_version
 * @property $key_pair_location
 * @property $ip_address
 * @property $private_ip_address
 * @property $forge_server_id
 * @property $forge_username
 * @property $database_username
 * @property $database_password
 * @property $aws_instance_id
 * @property $status
 * @property $credential_id
 * @property $created_at
 * @property $updated_at
 * @property $credential
 * @property $sites
 * @package App
 */
class Server extends Model
{
    // Fire ServerStatusUpdated Event each time a Server is created or updated
    protected $events = [
      'saved' => ServerStatusUpdated::class
    ];

    /**
     * Get the Credential that this Server belongs To
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function credential(){
        return $this->belongsTo('App\Credential');
    }

    /**
     * Get the Sites of his Server
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites(){
        return $this->hasMany('App\Site');
    }

}
