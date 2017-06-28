@extends('template.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <h1>Create Server</h1>

                <form action="{{ url('/launch') }}" method="post">

                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="aws_access_key_id">AWS Access Key ID</label>
                        <input name="aws_access_key_id" type="text" class="form-control" id="aws_access_key_id" placeholder="AWS Access Key ID">
                    </div>

                    <div class="form-group">
                        <label for="aws_secret_access_key">AWS Secret Access Key</label>
                        <input name="aws_secret_access_key" type="text" class="form-control" id="aws_secret_access_key" placeholder="AWS Secret Access Key">
                    </div>

                    <div class="form-group">
                        <label for="server_name">Server Name</label>
                        <input name="server_name" type="text" class="form-control" id="server_name" placeholder="Server Name">
                    </div>

                    <div class="form-group">
                        <label for="server_size">Server Size</label>
                        <select name="server_size" id="server_size" class="form-control">
                            <option value="512MB">0.5 GiB RAM - 1 vCPU</option>
                            <option value="1GB">1.0 GiB RAM - 1 vCPU</option>
                            <option value="2GB">2.0 GiB RAM - 1 vCPU</option>
                            <option value="4GB">4.0 GiB RAM - 2 vCPU</option>
                            <option value="8GB">8.0 GiB RAM - 2 vCPU</option>
                            <option value="16GB">16.0 GiB RAM - 4 vCPU</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="region">Region</label>
                        <select name="region" id="region" class="form-control">
                            <option value="us-east-1">Virginia</option>
                            <option value="ap-northeast-1">Tokyo</option>
                            <option value="ap-southeast-2">Sydney</option>
                            <option value="ap-southeast-1">Singapore</option>
                            <option value="ap-northeast-2">Seoul</option>
                            <option value="sa-east-1">Sao Paulo</option>
                            <option value="us-west-2">Oregon</option>
                            <option value="us-east-2">Ohio</option>
                            <option value="us-west-1">N. California</option>
                            <option value="ap-south-1">Mumbai</option>
                            <option value="eu-west-2">London</option>
                            <option value="eu-central-1">Frankfurt</option>
                            <option value="eu-west-1">Ireland</option>
                            <option value="ca-central-1">Central Canada</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="php_version">PHP Version</label>
                        <select name="php_version" id="php_version" class="form-control">
                            <option value="php71">7.1</option>
                            <option value="php70">7.0</option>
                            <option value="php56">5.6</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="domain_name">Domain Name</label>
                        <input name="domain_name" type="text" class="form-control" id="domain_name" placeholder="Domain Name">
                    </div>

                    <div class="form-group">
                        <label for="email_address">Email Address</label>
                        <input name="email_address" type="text" class="form-control" id="email_address" placeholder="Email Address">
                    </div>

                    <button type="submit" class="btn btn-default">Submit</button>
                </form>


            </div>
        </div>
    </div>

@endsection