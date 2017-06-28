<?php

namespace App\Http\Controllers;

use App\Helpers\ForgeHelpers;
use App\Jobs\EnableRootSSH;
use App\Jobs\LaunchInstance;
use App\Jobs\ProvisionInstance;
use App\Jobs\ReplaceDefaultSite;
use App\Jobs\RunServerUpdate;
use App\Launcher;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LaunchController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('launch.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Store the Information in the Database
        $launcher = new Launcher();
        $launcher->aws_access_key_id = $request->aws_access_key_id;
        $launcher->aws_secret_access_key = $request->aws_secret_access_key;
        $launcher->server_name = $request->server_name;
        $launcher->server_size = $request->server_size;
        $launcher->region = $request->region;
        $launcher->php_version = $request->php_version;
        $launcher->domain_name = $request->domain_name;
        $launcher->email_address = $request->email_address;
        $launcher->save();

        /*
         * Create a Server / Instance
         * based on the provided information
         */
        $this->dispatch( new LaunchInstance($launcher) );

        /*
         * Update the Server to allow root access
         */
        $this->dispatch( new EnableRootSSH($launcher) );

        /*
         * Ask Forge to Provision the Server
         */
        $this->dispatch( new ProvisionInstance($launcher) );

        /*
         * Run Commands to update the server (to latest Kernel)
         */
        $this->dispatch( new RunServerUpdate( $launcher ) );

        /*
         * Delete the Default Site and Replace it with Blogger's Site
         */
        $this->dispatch( new ReplaceDefaultSite( $launcher) );

        /*
         * Send Email with Server Details to the User
         */


        return 'Jobs Launched - Hang Tight';

    }


    public function testLauncher(Request $request){

        // echo ForgeHelpers::is_site_ready( '116094' , '279702' );
        // https://forge.laravel.com/servers/116094/sites/279702
        echo 'test function is empty';
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
