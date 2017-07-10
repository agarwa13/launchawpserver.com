@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-body">
                    <p>Hi {{ Auth::user()->name }}!</p>

                    <p>
                        I am going to guide you through three
                        simple steps.
                    </p>

                    <p>
                        At the end of these steps, you will have
                        a WordPress site that is running on
                        Amazon AWS <br> (sites like Netflix and Adobe
                        run on AWS so you are in good company).
                    </p>

                    <p>
                        Before we get started, I want you to know
                        that you are in good hands.
                    </p>

                    <p>
                        Don't worry if you don't understand
                        some of the terms you see throughout this
                        site.
                    </p>

                    <p>
                        I have built this service for beginners.
                    </p>

                    <p>
                        This means you simply cannot go wrong.
                        I don't let you make any bad choices.
                    </p>

                    <p>
                        So, Shall we get started?
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
                    <div class="panel-heading">What is Step 1?</div>
                    <div class="panel-body">
                        <p>
                            Like I mentioned before, we are going to
                            build your website on Amazon AWS.
                        </p>

                        <p>
                            Before I can build your website on AWS,
                            we need to connect your account here on
                            Launch a WP Server with your Amazon AWS
                            Account.
                        </p>

                        <p>
                            So, in the video below, I will walk you through,
                            click by click, everything you need to do to create
                            an Amazon AWS Account and provide Launch a WP Server
                            access to your AWS Account.
                        </p>

                        <p>
                            A few quick tips before I show you the video:
                        </p>
                        <ul>
                            <li>
                                Use a Desktop or Laptop to implement the steps from
                                the video. Don't try to sign up on your mobile phone.
                                A tablet might be okay, but a Desktop or Laptop is
                                preferable.
                            </li>

                            <li>
                                If you have two monitors, keep the video on one monitor
                                and follow along on the second monitor.
                            </li>

                            <li>
                                If you don't have two monitors, you can run the video
                                on your mobile phone or tablet and implement the steps
                                on your laptop or desktop.
                            </li>

                        </ul>

                        <p>
                            Ready to Watch the Video?
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
                <div class="panel panel-default">
                    <div class="panel-heading">Video Tutorial: Create an AWS Account</div>
                    <div class="panel-body">

                        <p>
                            Here you go.
                        </p>

                        <p>
                            If you want to watch this video on your mobile phone or some
                            other device, simply open this link on that device:
                            !!!NEED TO INSERT VIMEO LINK HERE!!!
                        </p>

                        <p>
                            Otherwise, just click on play below.
                        </p>

                        <p>
                            Once you have followed the steps from the video,
                            just click on I have my credentials.
                        </p>

                        <!-- 16:9 aspect ratio -->
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe src="https://player.vimeo.com/video/211135201" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        </div>

                    </div>

                    <div class="panel-footer">
                        <div class="pull-right">
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#panel-3" aria-expanded="false" aria-controls="panel-3">
                                I have my Credentials
                            </button>

                            <a class="btn btn-muted" href="{{ url('hand-held-launch') }}">
                                I need more help
                            </a>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <div class="collapse" id="panel-3">
                @include('credentials.add-credentials-form')
            </div>

        </div>
    </div>
</div>
@endsection
