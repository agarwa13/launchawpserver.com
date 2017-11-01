@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                @include('sites.add-site-form')

                <div class="panel panel-default">
                    <div class="panel-heading">Active Sites</div>
                    <table class="table">

                        <tr>
                            <td>Domain Name</td>
                            <td>Status</td>
                            <td>SSL</td>
                            <td></td>
                        </tr>

                        @forelse($server->sites as $site)
                            <tr>
                                <td><a href="{{url('/sites/'.$site->id)}}">{{ $site->domain_name }}</a></td>
                                <td>{{$site->status}}</td>
                                <td><!-- TODO: Site SSL Status --></td>
                                <td>
                                    <button class="btn btn-danger btn-circle">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        <span class="sr-only">Delete Site</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    No Sites have been Added
                                </td>
                            </tr>
                        @endforelse

                    </table>
                </div>


            </div>
        </div>
    </div>

@endsection