<?php

namespace App\Jobs;

use App\Helpers\SSHHelpers;
use App\Launcher;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class RunServerUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $launcher;

    /**
     * RunServerUpdate constructor.
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
        // SSH Into the Instance
        $ssh = SSHHelpers::instance_available_for_ssh( 'root' , $this->launcher->ip_address , Storage::get($this->launcher->key_pair_location) );

        // Run the Commands to Update the Server
        $ssh->exec( 'apt-get update' );
        $ssh->exec( 'apt-get upgrade' );
        $ssh->exec( 'apt-get dist-upgrade' );

        // Update the Status of the Server
        $this->launcher->status = 'Server Updated';
        $this->launcher->save();

    }
}
