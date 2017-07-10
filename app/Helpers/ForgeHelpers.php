<?php
/**
 * Created by PhpStorm.
 * User: Nikhil Agarwal
 * Date: 6/25/17
 * Time: 10:16 PM
 */

namespace App\Helpers;

use GuzzleHttp\Client;



class ForgeHelpers
{

    public static function wait_until_database_is_ready( $server_id, $database_id ){
        // We wait for 5 seconds before sending the first check up request
        sleep(5);

        // Check every 2 minutes, up to 20 times if the Database is Ready
        for($i = 0; $i < 20; $i++){
            if(self::is_database_ready($server_id, $database_id)){
                return true;
            }
            sleep(2*60);
        }

        // Return False if the Site is not yet deleted
        return false;
    }

    private static function is_database_ready($server_id, $database_id){
        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to get the Database Information
        $response = $client->request('GET','servers/'.$server_id.'/mysql/'.$database_id);

        // Return True is the Status of the Database is Ready
        $json = json_decode((string) $response->getBody(), true);
        return ($json['database']['status'] == 'installed');
    }

    public static function wait_until_site_is_ready( $server_id, $site_id){
        // We wait for 5 seconds before sending the first check up request
        sleep(5);

        // Check every 2 minutes, up to 20 times if the Site is Ready
        for($i = 0; $i < 20; $i++){
            if(self::is_site_ready($server_id, $site_id)){
                return true;
            }
            sleep(2*60);
        }

        // Return False if the Site is not yet deleted
        return false;
    }


    private static function is_site_ready($server_id, $site_id){

        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to get the Site Information
        $response = $client->request('GET','servers/'.$server_id.'/sites/'.$site_id);

        // Return True is the Status of the Site is Ready
        $json = json_decode((string) $response->getBody(), true);
        return ($json['site']['status'] == 'installed');

    }


    /**
     * @param $server_id
     * @param $site_id
     * @return bool
     */
    public static function wait_until_site_deleted( $server_id, $site_id ){

        // We wait for 5 seconds before sending the first check up request
        sleep(5);

        // Check every 2 minutes, up to 20 times if the Site is Deleted
        for($i = 0; $i < 20; $i++){
            if(self::is_site_deleted($server_id, $site_id)){
                return true;
            }
            sleep(2*60);
        }

        // Return False if the Site is not yet deleted
        return false;
    }

    /**
     * @param $server_id
     * @param $site_id
     * @return bool
     */
    private static function is_site_deleted( $server_id, $site_id ){

        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to get the Site Information
        $response = $client->request('GET','servers/'.$server_id.'/sites/'.$site_id);

        // Return True if the site doesn't exist
        if($response->getStatusCode() == 404 ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check if the Server is Ready
     * If Ready, Return True
     * If not, Check Again after 2 minutes
     * Return False if Server is still not ready after 40 minutes
     * @param $server_id
     * @return bool
     */
    public static function wait_until_server_ready( $server_id ){
        for($i = 0; $i < 20; $i++){
            if(self::is_server_ready($server_id)){
                return true;
            }
            sleep(2*60);
        }
        return false;
    }

    /**
     * @param $server_id
     * @return bool
     */
    private static function is_server_ready($server_id){

        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to get the Server Information
        $response = $client->request('GET','servers/'.$server_id);

        // Return the value of the is_ready attribtue
        $json = json_decode((string) $response->getBody(), true);
        return $json['server']['is_ready'];

    }

    private static function get_forge_client(){

        // These Headers will be sent with every request to Forge unless overridden
        $headers = [
            'Authorization' => 'Bearer ' . env('FORGE_TOKEN'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        // Create a Client to Communicate with Forge
        $client = new Client([
            'base_uri' => 'https://forge.laravel.com/api/v1/',
            'headers' => $headers,
            'http_errors' => false
        ]);

        return $client;

    }

    /**
     * @param $name
     * @param $size
     * @param $ip_address
     * @param $php_version
     * @param $database
     * @param $private_ip_address
     * @return mixed
     */
    public static function create_server( $name, $size, $ip_address, $php_version, $database, $private_ip_address ){

        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to Forge to Create the Server
        $response = $client->request('POST','servers', ['json' => [
            'provider'=> 'custom',
            'name' => $name,
            'size' => $size,
            'ip_address' => $ip_address,
            'php_version' => $php_version,
            'database' => $database,
            'private_ip_address' => $private_ip_address
        ]]);

        // Return the response
        return json_decode((string) $response->getBody(), true);

    }

    /**
     * Gets the Sites
     * @param $server_id
     * @return mixed
     */
    public static function get_sites( $server_id ){

        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Get the Sites
        $response = $client->request('GET','servers/'.$server_id.'/sites');

        // Return the response as an Array
        return json_decode((string) $response->getBody(), true);

    }


    /**
     * @param $server_id
     * @param $site_id
     * @return bool
     */
    public static function delete_site( $server_id,  $site_id){

        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Delete the Site
        $client->request('DELETE','servers/'.$server_id.'/sites/'.$site_id);

        // Return true
        return true;
    }

    /**
     * @param $server_id
     * @param $domain
     * @param $project_type
     * @param $directory
     * @return mixed
     */
    public static function create_site( $server_id, $domain, $project_type, $directory ){

        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Create the Site
        $response = $client->request('POST','servers/'.$server_id.'/sites', ['json' => [
            'domain' => $domain,
            'project_type' => $project_type,
            'directory' => $directory
        ]]);

        // Return the response as an Array
        return json_decode((string) $response->getBody(), true);

    }


    /**
     * @param $server_id
     * @param $name
     * @return mixed
     */
    public static function create_database($server_id, $name){
        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Create the Database
        $response = $client->request('POST','servers/'.$server_id.'/mysql', ['json' => [
            'name' => $name
        ]]);

        // Return the response as an Array
        return json_decode((string) $response->getBody(), true);

    }

    /**
     * @param $server_id
     * @param $name
     * @param $password
     * @param $database_id
     * @return mixed
     */
    public static function create_database_user($server_id, $name, $password, $database_id){
        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Create the User
        $response = $client->request('POST','servers/'.$server_id.'/mysql-users', ['json' => [
            'name' => $name,
            'password' => $password,
            'databases' => [$database_id]
        ]]);

        // Return the response as an Array
        return json_decode((string) $response->getBody(), true);

    }


    public static function wait_until_database_user_is_ready($server_id, $user_id){
        for($i = 0; $i < 20; $i++){
            if(self::is_database_user_ready($server_id, $user_id)){
                return true;
            }
            sleep(2*60);
        }
        return false;
    }

    public static function is_database_user_ready($server_id, $user_id){

        // Get a Forge Client
        $client = self::get_forge_client();

        // Send a Request to get the Server Information
        $response = $client->request('GET','servers/'.$server_id.'/mysql-users/'.$user_id);

        // Return the value of the is_ready attribtue
        $json = json_decode((string) $response->getBody(), true);
        return ($json['user']['status'] == 'installed');

    }

    /**
     * @param $server_id
     * @param $site_id
     * @param $database
     * @param $user
     * @return mixed
     */
    public static function install_wordpress($server_id, $site_id, $database, $user){

        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Install WordPress
        $response = $client->request('POST','servers/'.$server_id.'/sites/'.$site_id.'/wordpress', ['json' => [
            'database' => $database,
            'user' => $user
        ]]);

        // Return the response as an Array
        return json_decode((string) $response->getBody(), true);

    }


    /**
     * @param $server_id
     */
    public static function restart_server($server_id){
        // Get a Forge Client
        $client = self::get_forge_client( );

        // Send a Request to Forge to Install WordPress
        $client->request('POST','servers/'.$server_id.'/reboot');

    }

    /**
     * @param $server_id
     */
    public static function reboot_server($server_id)
    {
        self::restart_server($server_id);
    }

}