@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                @include('servers.add-server-form')

                <div class="panel panel-default">
                    <div class="panel-heading">Active Servers</div>
                    <table class="table">

                        <tr>
                            <td>Name</td>
                            <td>Size</td>
                            <td>IP Address</td>
                            <td>Status</td>
                        </tr>

                        @forelse($servers as $server)
                            <tr>
                                <td>{{ $server->name }}</td>
                                <td>{{ $server->size }}</td>
                                <td>{{ $server->ip_address }}</td>
                                <td>
                                    <button class="btn btn-danger btn-circle">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        <span class="sr-only">Delete Server</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    No Servers have been Added
                                </td>
                            </tr>
                        @endforelse

                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
