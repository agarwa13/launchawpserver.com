<?php

namespace App\Jobs;

use App\Helpers\AWSHelpers;
use App\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\SSHHelpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UpgradeServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs{
        Dispatchable::dispatch insteadOf DispatchesJobs;
        DispatchesJobs::dispatch as jobDispatcher;
    }

    protected $server;

    public $timeout = 600;

    /**
     * Create a new job instance.
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
        $this->server->status = config('constants.server_upgrading');
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

        // Wait for 5 seconds
        sleep(5);

        /*
         * We get a New SSH Connection since the old one has probably timed out
         */
        $ssh = SSHHelpers::instance_available_for_ssh(
            'root',
            $this->server->ip_address,
            Storage::get($this->server->key_pair_location)
        );

        $filename = 'update-server-'.$this->server->id.'.sh';
        $file = 'public/'.$filename;

        // Create a copy of the Basic Update Server Script
        Storage::copy('public/update-server.sh', $file );

        // Append the Notification Line
        Storage::append($file, 'wget ' . url( 'servers/' . $this->server->id . '/server-upgraded' ) );

        // Copy the File to the Server
        $ssh->exec('wget ' . url($filename) );

        // Wait for the File to complete downloading
        sleep(5);

        // Run the File
        $ssh->exec('nohup bash '. $filename);

        // Wait Until Server is Upgraded
        while( !$this->is_server_upgraded($this->server->id) ){
            sleep(30);
        }

        // Restart the Server
        AWSHelpers::restart_server($this->server);

        //Wait 10 Seconds for Restart to Complete
        sleep(10);

        // Dispatch the Provisioning Server Job
        $this->jobDispatcher( new ProvisionInstance( $this->server ) );

        // Update the Status
        $this->server->status = config('constants.server_queued_for_provisioning');
        $this->server->save();

    }

    private function is_server_upgraded($id){
        $server = Server::findOrFail($id);
        return ( $server->status == config('constants.server_upgrade_complete') );
    }

    public function failed()
    {
        // When the Job Fails, we want to update the status of the Server to say so
        $this->server->status = config('constants.server_upgrade_failed');
        $this->server->save();
    }
}
