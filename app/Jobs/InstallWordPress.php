<?php

namespace App\Jobs;

use App\Helpers\ForgeHelpers;
use App\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laravel\Forge\Forge;

class InstallWordPress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs{
        Dispatchable::dispatch insteadOf DispatchesJobs;
        DispatchesJobs::dispatch as jobDispatcher;
    }

    protected $site;

    /*
     * Set the Timeout to be 5 minutes
     */
    public $timeout = 300;


    /**
     * Create a new job instance.
     * @param Site $site
     * @return void
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Update the Status to Installing WordPress
        $this->site->status = config('constants.site_installing_wordpress');
        $this->site->save();

        // Ask Forge to Install WordPress
        $forge_database = ForgeHelpers::create_database( $this->site->server->forge_server_id, $this->site->database_name );

        print_r($forge_database);
        
        $this->site->forge_database_id = $forge_database['database']['id'];
        $this->site->save();

        // Create a Database
        ForgeHelpers::wait_until_database_is_ready( $this->site->server->forge_server_id, $this->site->forge_database_id  );
        $forge_database_user = ForgeHelpers::create_database_user( $this->site->server->forge_server_id, $this->site->database_user_name, $this->site->database_user_name );

        // Create a User for the Database
        $this->site->forge_database_user_id = $forge_database_user['user']['id'];
        ForgeHelpers::wait_until_database_user_is_ready( $this->site->server->forge_server_id, $this->site->forge_database_user_id );

        // Install WordPress
        ForgeHelpers::install_wordpress($this->site->server->forge_server_id, $this->site->forge_site_id, $this->site->forge_database_id, $this->site->forge_database_user_id);
        ForgeHelpers::wait_until_wordpress_is_ready($this->site);

        // Update the Status
        $this->site->status = config('constants.site_ready_for_DNS_update');
        $this->site->save();

    }
}
