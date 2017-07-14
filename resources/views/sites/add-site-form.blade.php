<div class="panel panel-default">
    <div class="panel-heading">Launch Site</div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('sites') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('domain_name') ? ' has-error' : '' }}">
                <label for="domain_name" class="col-md-4 control-label">Domain Name</label>

                <!-- TODO: Auto Generate Names -->
                <div class="col-md-6">
                    <input id="domain_name" type="text" class="form-control" name="domain_name" value="{{ old('domain_name') }}" required>

                    @if ($errors->has('domain_name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('domain_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @if( isset($server) )
                <input name="server_id" id="server_id" value="{{$server->id}}" type="hidden">
            @else
            <div class="form-group{{ $errors->has('server_id') ? ' has-error' : '' }}">
                <label for="server_id" class="col-md-4 control-label">Server</label>

                <div class="col-md-6">
                    <select name="server_id" id="server_id" class="form-control" required>
                        @foreach(Auth::user()->servers as $server)
                            <option value="{{$server->id}}">{{$server->name}}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('server_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('server_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            @endif

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Launch Site
                    </button>

                    <!-- TODO: Create a Guide for Launching the Site -->
                    <a class="btn btn-link" href="{{ url('sites/guide') }}">
                        Need Help Launching a Site?
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>