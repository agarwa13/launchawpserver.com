<?php

namespace App\Http\Controllers;

use App\Credential;
use App\Jobs\AddAccessPolicies;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CredentialController extends Controller
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
     * Display the Guide to Creating Credentials
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function guide()
    {
        return view('credentials.guide');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('credentials.index')
            ->with('credentials',Auth::user()->credentials);
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
         */
        $this->validate($request, [
            'access_key_id' => 'required|unique:credentials',
            'secret_access_key' => 'required'
        ]);

        /*
         * Check if the User Exists and has the IAM Full Access Policy
         */
        $client = new IamClient([
            'credentials' => [
                'key' => $request->access_key_id,
                'secret' => $request->secret_access_key
            ],
            'region' => 'us-west-2',
            'version' => '2010-05-08'
        ]);


        $response = $client->getUser();

        // If the Credentials do not correspond to a User then Abort
        if($response->hasKey('User')){
            $name = $response['User']['UserName'];
        }else{
            return abort(422);
        }


        // Get the Policies of the User
        $response = $client->listAttachedUserPolicies(array(
            'UserName' => $name
        ));

        // Check if this User has any policies attached
        if(!$response->hasKey('AttachedPolicies')){
            abort(422);
        }


        // Check if the IAM Policy is Attached
        $policies = $response['AttachedPolicies'];
        $IAMPolicy_Found = false;
        foreach($policies as $policy){
            if($policy['PolicyName'] == 'IAMFullAccess'){
                $IAMPolicy_Found = true;
            }
        }

        if(!$IAMPolicy_Found){
            abort(422);
        }

        // Looks like Everything Checks out

        /*
         * Save the Credentials to the Database
         */
        $credential = new Credential();
        $credential->name = $name;
        $credential->access_key_id = $request->access_key_id;
        $credential->secret_access_key = $request->secret_access_key;
        $credential->user_id = $request->user()->id;
        $credential->save();

        flash('Credential Added')->success();

        // Launch a Job to Add the Required Policies
        // to this User
        $this->dispatch( new AddAccessPolicies($credential) );

        /*
         * If the user does not have any servers
         * let's redirect them to the servers guide
         */
        if(!$request->user()->servers->count()){
            return redirect('servers/guide');
        }

        return view('credentials.index')
            ->with('credentials',Auth::user()->credentials);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Credential  $credential
     * @return \Illuminate\Http\Response
     */
    public function show(Credential $credential)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Credential  $credential
     * @return \Illuminate\Http\Response
     */
    public function edit(Credential $credential)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Credential  $credential
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Credential $credential)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Credential  $credential
     * @return \Illuminate\Http\Response
     */
    public function destroy(Credential $credential)
    {
        //
    }
}
