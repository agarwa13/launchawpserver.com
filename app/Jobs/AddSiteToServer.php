<?php

namespace App\Jobs;

use App\Helpers\ForgeHelpers;
use App\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddSiteToServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60*15;

    protected $server;
    protected $site;

    /**
     * ReplaceDefaultSite constructor.
     * @param $site
     */
    public function __construct(Site $site)
    {
        $this->server = $site->server;
        $this->site = $site;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {

        $this->server->status = 'Building';
        $this->server->save();

        /*
         * Create a New Site using the Provided Details
         */
        $response = ForgeHelpers::create_site( $this->server->forge_server_id, $this->site->domain_name, 'php', '/public');

        /*
         * Check to Make Sure a Site was Returned or Throw an Error
         */
        if( !array_key_exists( 'site', $response) ){
            throw new \Exception('Failed to Create Site');
        }

        /*
         * Save the Forge Site ID to the Database
         */
        $this->site->forge_site_id = $response['site']['id'];

        /*
         * Wait Until the New Site is Ready
         */
        ForgeHelpers::wait_until_site_is_ready( $this->server->forge_server_id , $this->site->forge_site_id );

        /*
         * Generate and Save the WordPress Database Information
         */
        $this->server->wordpress_database_password = bin2hex(openssl_random_pseudo_bytes(4));
        $this->server->wordpress_database_name = 'wordpress'.$this->site->id;
        $this->server->wordpress_database_user = $this->server->wordpress_database_name;
        $this->server->save();

        /*
         * Create a Database for using with WordPress
         */
        $response = ForgeHelpers::create_database( $this->server->forge_server_id, $this->server->wordpress_database_name);

        var_export($response);

        /*
         * Check if Database was created
         */
        if( !array_key_exists( 'database', $response) ){
            var_export($response);
            throw new \Exception('Failed to Create Database');
        }

        /*
         * Store the Database ID
         */
        $database = $response['database'];
        $this->server->wordpress_database_id = $database['id'];
        $this->server->save();

        /*
         * Wait until the Database is Created
         */
        ForgeHelpers::wait_until_database_is_ready( $this->server->forge_server_id , $this->server->wordpress_database_id );

        /*
         * Create a User for the Database
         */
        $response = ForgeHelpers::create_database_user( $this->server->forge_server_id, $this->server->wordpress_database_user, $this->server->wordpress_database_password, $this->server->wordpress_database_id);

        var_export($response);

        /*
         * Store the Database User ID
         */
        $user = $response['user'];
        $this->server->wordpress_database_user_id = $user['id'];
        $this->server->save();

        /*
         * Wait Until User is Ready (Installed)
         */
        ForgeHelpers::wait_until_database_user_is_ready($this->server->forge_server_id,$this->server->wordpress_database_user_id );

        /*
         * Install WordPress on the Site once the Database and the User is Ready
         */
        ForgeHelpers::install_wordpress( $this->server->forge_server_id,
            $this->site->forge_site_id, $this->server->wordpress_database_id, $this->server->wordpress_database_user_id);

        /*
         * Update the Status to Indicate WordPress is installed
         */
        $this->server->status = 'Active';
        $this->server->save();
    }





}
