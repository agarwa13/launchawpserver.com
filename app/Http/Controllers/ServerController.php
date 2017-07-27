<?php

namespace App\Http\Controllers;

use App\Jobs\LaunchInstance;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServerController extends Controller
{

    /*
     * Specify Middleware for Routes
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     * Display the Guide to Creating a Server
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function guide()
    {
        return view('servers.guide');
    }


    /**
     * @param Request $request
     * @param Server $server
     * @return mixed
     */
    public function serverUpgraded(Request $request, Server $server){

        $server->status = config('constants.server_upgrade_complete');
        $server->save();

        return response()->file( public_path('server-upgraded-pixel.png') );
    }

    /*
     * Display a listing of the resource
     * @return View
     */
    public function index()
    {
        return view('servers.index')
            ->with('servers',Auth::user()->servers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        /*
         * Do Basic Validation of the Inputs
         */
        $this->validate($request, [
            'name' => 'required|alpha_dash',
            'size' => [ 'required', Rule::in([
                '512MB',
                '1GB',
                '2GB',
                '4GB',
                '8GB',
                '16GB'
            ]) ],
            'region' => [ 'required', Rule::in([
                'us-east-1',
                'ap-northeast-1',
                'ap-southeast-2',
                'ap-southeast-1',
                'ap-northeast-2',
                'sa-east-1',
                'us-west-2',
                'us-east-2',
                'us-west-1',
                'ap-south-1',
                'eu-west-2',
                'eu-central-1',
                'eu-west-1',
                'ca-central-1'
            ]) ]
        ]);


        /*
         * Add to Database
         */
        $server = new Server();

        /*
         * Information from Request
         */
        $server->name = $request->name;
        $server->size = $request->size;
        $server->region = $request->region;
        $server->credential_id = $request->user()->credentials->first()->id;
        /*
         * Default Information
         */
        $server->php_version = config('constants.default_php_version');
        $server->status = config('constants.server_queued_for_building');
        $server->save();

        /*
         * Queue a Job to Launch the Instance on Amazon AWS
         */
        dispatch( new LaunchInstance( $server ) );

        /*
         * Flash Success Message
         */
        flash('Server is Queued for Building');

        return view('servers.index')
            ->with('servers',Auth::user()->servers);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        return view('servers.show')
            ->with('server',$server);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        //
    }
}
