<?php

namespace App\Jobs;

use App\Helpers\ForgeHelpers;
use App\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\SSHHelpers;
use Illuminate\Support\Facades\Storage;


class ProvisionInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    /**
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a New Job Instance
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

        // Update the Status
        $this->server->status = 'Provisioning';
        $this->server->save();

        // Wait until we can SSH into the instance
        $ssh = SSHHelpers::instance_available_for_ssh(
            'ubuntu',
            $this->server->ip_address,
            Storage::get($this->server->key_pair_location)
        );

        // Update the Server so we can SSH as Root Instead of Ubuntu
        $ssh->exec('sudo sed -i \'s/prohibit-password/yes/\' /etc/ssh/sshd_config');
        $ssh->exec('sudo cp /home/ubuntu/.ssh/authorized_keys /root/.ssh/');

        // Create a Server on Forge
        $json = ForgeHelpers::create_server(
            $this->server->name,
            $this->server->size,
            $this->server->ip_address,
            $this->server->php_version,
            config('constants.default_database_username'),
            $this->server->private_ip_address
        );

        // Store Relevant Information from Forge in the Database
        $this->server->forge_server_id = (string) $json['server']['id'];
        $this->server->forge_username = 'forge';
        $this->server->database_username = config('constants.default_database_username');
        $this->server->database_password = $json['database_password'];
        $this->server->save();

        // Wait until we can SSH into the instance
        $ssh = SSHHelpers::instance_available_for_ssh(
            'root',
            $this->server->ip_address,
            Storage::get($this->server->key_pair_location)
        );

        // Download the File from Forge
        $arr = explode(" bash", $json['provision_command'], 2);
        $download_file_command = $arr[0];
        $ssh->exec($download_file_command);

        // Wait for the File to complete downloading
        sleep(5);

        // Run the File
        $ssh->exec('bash forge.sh');

        /*
         * Wait Until Server is Ready
         */
        ForgeHelpers::wait_until_server_ready( $this->server->forge_server_id );

        // Run the Commands to Update the Server
        $ssh->exec( 'apt-get update' );
        $ssh->exec( 'apt-get upgrade' );
        $ssh->exec( 'apt-get dist-upgrade' );

        //Get the Sites of the Server
        $response = ForgeHelpers::get_sites( $this->server->forge_server_id );


        //Delete the Default Site
        $sites = $response['sites'];
        foreach($sites as $site){
            if($site['name'] == 'default' ){
                ForgeHelpers::delete_site( $this->server->forge_server_id, $site['id'] );
                $deleted_site_id = $site['id'];
            }
        }

        //Wait until the Site is Deleted
        if( isset($deleted_site_id) ){
            ForgeHelpers::wait_until_site_deleted( $this->server->forge_server_id , $deleted_site_id );
        }

        // Restart the Server
        ForgeHelpers::restart_server($this->server->forge_server_id);

        // Wait until we can SSH into the instance
        SSHHelpers::instance_available_for_ssh(
            'ubuntu',
            $this->server->ip_address,
            Storage::get($this->server->key_pair_location)
        );

        // Update the Status of the Server
        $this->server->status = 'Active';
        $this->server->save();

    }
}
