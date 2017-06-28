<?php

namespace App\Jobs;

use App\Helpers\ForgeHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Laravel\Forge\Forge;

class ReplaceDefaultSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60*15;
    protected $launcher;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($launcher)
    {
        $this->launcher = $launcher;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
         * Wait Until Server is Ready
         */
        ForgeHelpers::wait_until_server_ready( $this->launcher->forge_server_id );

        /*
         * Get the Sites of the Server
         */
        $sites = ForgeHelpers::get_sites( $this->launcher->forge_server_id );

        /*
         * Delete the Default Site
         */
        foreach($sites as $site){
            if($site['name'] == 'default' ){
                ForgeHelpers::delete_site( $this->launcher->forge_server_id, $site['id'] );
                $deleted_site_id = $site['id'];
            }
        }

        if( isset($deleted_site_id) ){
            ForgeHelpers::wait_until_site_deleted( $this->launcher->forge_server_id , $deleted_site_id );
        }

        /*
         * Create a New Site using the Provided Details
         */
        $site = ForgeHelpers::create_site( $this->launcher->domain_name, 'php', '/public');

        /*
         * Wait Until the New Site is Ready
         */
        ForgeHelpers::wait_until_site_is_ready( $this->launcher->forge_server_id , $site['id'] );

        /*
         * Generate and Save the WordPress Database Information
         */
        $this->launcher->wordpress_database_password = bin2hex(openssl_random_pseudo_bytes(4));
        $this->launcher->wordpress_database_name = 'wordpress';
        $this->launcher->wordpress_database_user = 'wordpress';
        $this->launcher->save();

        /*
         * Create a Database for using with WordPress
         */
        $database = ForgeHelpers::create_database( $this->launcher->wordpress_database_name,
            $this->launcher->wordpress_database_user , $this->launcher->wordpress_database_password );

        /*
         * Wait until the Database is Created
         */
        ForgeHelpers::wait_until_database_is_ready( $this->launcher->forge_server_id , $database['id'] );

        /*
         * Install WordPress on the Site once the Database and the Site is Ready
         */
        ForgeHelpers::install_wordpress( $this->launcher->forge_server_id,
            $site['id'], $this->launcher->wordpress_database_name, $this->launcher->wordpress_database_user);

        /*
         * Update the Status to Indicate WordPress is installed
         */
        $this->launcher->status = 'WordPress Installation Complete';
        $this->launcher->save();
    }





}
