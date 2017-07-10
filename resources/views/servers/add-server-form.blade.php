<div class="panel panel-default">
    <div class="panel-heading">Launch Server</div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('servers') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('namae') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Name</label>

                <!-- TODO: Auto Generate Names -->
                <div class="col-md-6">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('size') ? ' has-error' : '' }}">
                <label for="size" class="col-md-4 control-label">Size</label>

                <div class="col-md-6">
                    <select name="size" id="size" class="form-control" required>
                        <option value="512MB">Nano - 0.5 GiB RAM - 1 vCPU</option>
                        <option value="1GB" selected="selected">Micro - 1.0 GiB RAM - 1 vCPU</option>
                        <option value="2GB">Small - 2.0 GiB RAM - 1 vCPU</option>
                        <option value="4GB">Medium - 4.0 GiB RAM - 2 vCPU</option>
                        <option value="8GB">Large - 8.0 GiB RAM - 2 vCPU</option>
                        <option value="16GB">Extra Large - 16.0 GiB RAM - 4 vCPU</option>
                    </select>
                    {{--<input id="size" type="text" class="form-control" name="size" required>--}}

                    @if ($errors->has('size'))
                        <span class="help-block">
                            <strong>{{ $errors->first('size') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }}">
                <label for="region" class="col-md-4 control-label">Region</label>

                <div class="col-md-6">
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

                    @if ($errors->has('region'))
                        <span class="help-block">
                            <strong>{{ $errors->first('region') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Launch Server
                    </button>

                    <a class="btn btn-link" href="{{ url('servers/guide') }}">
                        Need Help Launching a Server?
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>