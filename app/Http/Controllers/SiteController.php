<?php

namespace App\Http\Controllers;

use App\Helpers\ForgeHelpers;
use App\Jobs\LaunchSite;
use App\Server;
use App\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /*
         * Do Basic Validation of the Inputs
         * TODO: Need to add a validation rule for a domain name
         */
        $this->validate($request, [
            'domain_name' => 'required',
            'server_id' => [
                'required',
                'exists:servers,id'
            ]
        ]);


        /*
         * Add to Database
         */
        $site = new Site();

        /*
         * Information from Request
         */
        $site->domain_name = $request->domain_name;
        $site->server_id = $request->server_id;

        /*
         * Generate WordPress Database Credentials
         */
        $site->database_name =  ForgeHelpers::getRandomDatabaseName();
        $site->database_user_name = ForgeHelpers::getRandomDatabaseUserName();
        $site->database_user_password = ForgeHelpers::getRandomDatabaseUserPassword();
        $site->status = config('constants.site_queued_for_building');
        $site->save();

        /*
         * Queue a Job to Launch the Instance on Forge
         */
        dispatch( new LaunchSite( $site ) );

        /*
         * Flash Success Message
         */
        flash('Site is Queued for Building');

        /*
         * Show the Page from which the Site was Created.
         */
        return redirect('servers/'.$site->server_id);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        //
    }
}
