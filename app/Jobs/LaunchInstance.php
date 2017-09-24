<?php

namespace App\Jobs;

use App\Helpers\AWSHelpers;
use App\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ProvisionInstance;

class LaunchInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs{
        Dispatchable::dispatch insteadOf DispatchesJobs;
        DispatchesJobs::dispatch as jobDispatcher;
    }

    protected $server;

    /*
     * Set the Timeout to be 5 minutes
     */
    public $timeout = 300;

    /**
     * Create a new job instance
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // Update the Status to Building
        $this->server->status = config('constants.server_building');
        $this->server->save();

        // Create a Key Pair
        $keyPairName = 'key-pair-'.$this->server->id;
        $result = AWSHelpers::create_key_pair($this->server, $keyPairName);

        // Save the Private key to Disk
        $saveKeyLocation = storage_path('app/'.$keyPairName.".pem");
        file_put_contents( $saveKeyLocation ,  $result['KeyMaterial'] );

        // Save the location to Database for later retrieval using the Storage::get function
        $this->server->key_pair_location = $keyPairName.".pem";
        $this->server->save();

        // Update the key's permissions so it can be used with SSH
        chmod( $saveKeyLocation , 0600);

        // Create the Security Group
        $securityGroupName = AWSHelpers::create_security_group($this->server);

        // Launch an instance with the recently created key pair and security group
        AWSHelpers::launchInstance( $this->server, $keyPairName, $securityGroupName );

        // Wait until the instance is Fully Launched
        AWSHelpers::wait_until_instance_is_ready( $this->server );

        // Store the IP Address in the Database
        AWSHelpers::update_ip_addresses_in_database( $this->server );

        // Launch a Job to Upgrade the Server
        $this->jobDispatcher( new UpgradeServer( $this->server) );

        // Update the Status
        $this->server->status = config('constants.server_queued_for_upgrading');
        $this->server->save();

    }

    public function failed()
    {
        // When the Job Fails, we want to update the status of the Server to say so
        $this->server->status = config('constants.server_build_failed');
        $this->server->save();
    }
}
