@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @include('credentials.add-credentials-form')

            <div class="panel panel-default">
                <div class="panel-heading">Active Credentials</div>
                <table class="table">

                    <tr>
                        <td>Name</td>
                        <td>Access Key ID</td>
                        <td></td>
                    </tr>

                    @forelse($credentials as $credential)
                    <tr>
                        <td>{{ $credential->name }}</td>
                        <td>{{ $credential->access_key_id }}</td>
                        <td>
                            <button class="btn btn-danger btn-circle">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                <span class="sr-only">Delete Credentials</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                No Credentials have been Added
                            </td>
                        </tr>
                    @endforelse

                </table>
            </div>

        </div>
    </div>
</div>
@endsection
