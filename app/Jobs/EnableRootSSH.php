<?php

namespace App\Jobs;

use App\Helpers\SSHHelpers;
use App\Launcher;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Illuminate\Support\Facades\Storage;


class EnableRootSSH implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $launcher;

    /**
     * Allow up to 5 minutes
     * @var int
     */
    public $timeout = 300;

    /**
     * EnableRootSSH constructor.
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
        // Wait until we can SSH into the instance
        $ssh = SSHHelpers::instance_available_for_ssh(
            'ubuntu',
            $this->launcher->ip_address,
            Storage::get($this->launcher->key_pair_location)
        );

        // Update the Server so we can SSH as Root Instead of Ubuntu
        $ssh->exec('sudo sed -i \'s/prohibit-password/yes/\' /etc/ssh/sshd_config');
        $ssh->exec('sudo cp /home/ubuntu/.ssh/authorized_keys /root/.ssh/');

        /*
         * Update the Status
         */
        $this->launcher->status = 'Root SSH Enabled';
        $this->launcher->save();


    }

//    /**
//     * @param $instance_ip_address
//     * @param $private_key
//     * @return SSH2|bool
//     */
//    private function try_ssh($instance_ip_address, $private_key){
//
//        // Create SSH
//        $ssh = new SSH2($instance_ip_address);
//
//        // Prepare Key
//        $key = new RSA();
//        $key->loadKey($private_key);
//
//        /*
//         * Try to Login
//         * If Failed or Unsuccessful, return false
//         * Else return the SSH Object
//         */
//        try{
//            if (!$ssh->login('ubuntu', $key)) {
//                return false;
//            }else{
//                return $ssh;
//            }
//        }catch (\Exception $exception){
//            return false;
//        }
//
//    }
//
//    /**
//     * @param $instance_ip_address
//     * @param $private_key
//     * @return SSH2
//     */
//    private function instance_available_for_ssh($instance_ip_address, $private_key){
//
//        /*
//         * Try to connect to the instance
//         * If connection is successful
//         * return the connection
//         * else, wait for 30 seconds and try again
//         * up to 30 times
//         */
//        for($i = 0; $i < 30 ; $i++ ){
//            $result = $this->try_ssh($instance_ip_address, $private_key);
//            if($result){
//                return $result;
//            }else{
//                sleep(30);
//            }
//        }
//
//        exit('Unable to SSH');
//
//    }

}
