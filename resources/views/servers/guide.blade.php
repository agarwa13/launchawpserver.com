@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-body">
                    <p>Good Job {{ Auth::user()->name }}!</p>

                    <p>
                        You have finished the hardest part! Really, it is easy
                        as pie from here on out.
                    </p>

                    <p>
                        Now that Launch a WP Server has access to your AWS Account,
                        it can do a lot for you!
                    </p>

                    <p>
                        In fact, we are going to do step 2 now, which is launch
                        a web server for your sites.
                    </p>

                    <p>
                        A Web Server is a scary term to the uninitiated but it really
                        isn't that complicated. A Web Server is a computer that runs around
                        the clock. Anytime someone tries to visit your website, this
                        computer sends your website to them! That's it :).
                    </p>

                    <p>
                        Now Amazon AWS allows you to run a small web server
                        for your first 12 months for Free!
                    </p>

                    <p>
                        Isn't that pretty awesome?
                    </p>

                    <p>
                        So, Shall we get started and create our first server?
                    </p>

                </div>

                <div class="panel-footer">
                    <div class="pull-right">
                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#panel-1" aria-expanded="false" aria-controls="panel-1">
                            Yes
                        </button>

                        <button class="btn btn-muted" type="button" data-toggle="collapse" data-target="#panel-no" aria-expanded="false" aria-controls="panel-no">
                            Not Yet. I have some questions.
                        </button>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="collapse" id="panel-1">
                <div class="panel panel-default">
                    <div class="panel-heading">What is Step 2?</div>
                    <div class="panel-body">
                        <p>
                            Like I mentioned before, we are going to
                            launch a web server.
                        </p>

                        <p>
                            I just need to know a couple of things from
                            you before I can launch your server.
                        </p>


                        <p>
                            <strong>Name:</strong><br>
                            I need a name for your server. You will see
                            that I have suggested a name for you, but you can
                            change it to something more useful if you think you
                            need multiple servers.
                        </p>

                        <p>
                            <strong>Size:</strong><br>
                            I also need to know the 'size' of server you
                            want to launch. This is a measure of how
                            powerful your server is. As your website attracts
                            more visitors, we will need to increase the size of
                            the server.
                        </p>

                        <p>
                            Amazon provides the Micro Server for Free. Don't
                            let the name fool you. A Micro server is quite
                            capable of handling tens and even hundreds of
                            thousands of visitors per month. This is what I
                            recommend you use if you are just starting out.
                        </p>

                        <p>
                            <strong>Region</strong><br>
                            Finally, I need to know the region where I should
                            setup your Server.
                        </p>

                        <p>
                            Amazon has servers available all over the world.
                            You want to pick the server that is geographically
                            closest to the people visiting your website.
                        </p>

                        <p>
                            For example, if you are building a website that is
                            for a local business in San Diego, you should select
                            the location that is closest to San Diego.
                        </p>


                        <p>
                            Ready to Launch your Server?
                        </p>


                    </div>

                    <div class="panel-footer">
                        <div class="pull-right">
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#panel-2" aria-expanded="false" aria-controls="panel-2">
                                Yes
                            </button>

                            <button class="btn btn-muted" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                Not Yet. I have some questions.
                            </button>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <div class="collapse" id="panel-2">
                @include('servers.add-server-form')
            </div>

        </div>
    </div>
</div>
@endsection
