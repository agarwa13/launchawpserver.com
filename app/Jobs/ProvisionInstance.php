<?php

namespace App\Jobs;

use App\Helpers\ForgeHelpers;
use App\Launcher;
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

    protected $launcher;

    /**
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a New Job Instance
     * @param Launcher $launcher
     */
    public function __construct(Launcher $launcher)
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

        $json = ForgeHelpers::create_server(
            $this->launcher->server_name,
            $this->launcher->server_size,
            $this->launcher->ip_address,
            $this->launcher->php_version,
            'forge',
            $this->launcher->private_ip_address
        );

        // Store Relevant Information In the Database

        $this->launcher->forge_server_id = (string) $json['server']['id'];
        $this->launcher->forge_username = 'forge';
        $this->launcher->database_username = 'forge';
        $this->launcher->database_password = $json['database_password'];
        $this->launcher->save();

        // Wait until we can SSH into the instance
        $ssh = SSHHelpers::instance_available_for_ssh(
            'root',
            $this->launcher->ip_address,
            Storage::get($this->launcher->key_pair_location)
        );

        // Download the File from Forge
        $arr = explode(" bash", $json['provision_command'], 2);
        $download_file_command = $arr[0];
        $ssh->exec($download_file_command);

        // Wait for the File to complete downloading
        sleep(5);

        // Run the File
        $ssh->exec('bash forge.sh');

        // Update the Status
        $this->launcher->status = 'Provisioning Script Started';
        $this->launcher->save();

    }
}
