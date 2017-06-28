<?php
/**
 * Created by PhpStorm.
 * User: nikhilagarwal
 * Date: 6/25/17
 * Time: 4:30 PM
 */

namespace App\Helpers;



use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class SSHHelpers
{


    /**
     * @param $user
     * @param $instance_ip_address
     * @param $private_key
     * @return bool|SSH2
     */
    private static function try_ssh($user, $instance_ip_address, $private_key){

        // Create SSH
        $ssh = new SSH2($instance_ip_address);

        // Prepare Key
        $key = new RSA();
        $key->loadKey($private_key);

        /*
         * Try to Login
         * If Failed or Unsuccessful, return false
         * Else return the SSH Object
         */
        try{
            if (!$ssh->login($user, $key)) {
                return false;
            }else{
                return $ssh;
            }
        }catch (\Exception $exception){
            return false;
        }

    }

    /**
     * @param $user
     * @param $instance_ip_address
     * @param $private_key
     * @return bool|SSH2
     */
    public static function instance_available_for_ssh($user, $instance_ip_address, $private_key){

        /*
         * Try to connect to the instance
         * If connection is successful
         * return the connection
         * else, wait for 30 seconds and try again
         * up to 30 times
         */
        for($i = 0; $i < 30 ; $i++ ){
            $result = SSHHelpers::try_ssh($user, $instance_ip_address, $private_key);
            if($result){
                return $result;
            }else{
                sleep(30);
            }
        }

        exit('Unable to SSH');

    }
}