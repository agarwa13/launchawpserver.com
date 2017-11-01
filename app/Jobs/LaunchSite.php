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

class LaunchSite implements ShouldQueue
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
        // Update the Status to Building
        $this->site->status = config('constants.site_building');
        $this->site->save();

        // Ask Forge to Create the Site
        $forge_site = ForgeHelpers::create_site($this->site->server->forge_server_id, $this->site->domain_name);

        print_r($forge_site);

        // Update the Database with the Site ID
        $this->site->forge_site_id = (string) $forge_site['site']['id'];
        $this->site->save();

        // Wait until Site is Installed
        ForgeHelpers::wait_until_site_is_ready($this->site->server->forge_server_id, $this->site->forge_site_id);

        // Launch a Job to Install WordPress
        $this->jobDispatcher( new InstallWordPress( $this->site) );

        // Update the Status
        $this->site->status = config('constants.site_queued_for_wordpress_installation');
        $this->site->save();


    }
}
