<div class="panel panel-default">
    <div class="panel-heading">Add Credentials</div>
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('credentials') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('access_key_id') ? ' has-error' : '' }}">
                <label for="access_key_id" class="col-md-4 control-label">Access key ID</label>

                <div class="col-md-6">
                    <input id="access_key_id" type="text" class="form-control" name="access_key_id" value="{{ old('access_key_id') }}" required>

                    @if ($errors->has('access_key_id'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('access_key_id') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('secret_access_key') ? ' has-error' : '' }}">
                <label for="secret_access_key" class="col-md-4 control-label">Secret Access Key</label>

                <div class="col-md-6">
                    <input id="secret_access_key" type="text" class="form-control" name="secret_access_key" required>

                    @if ($errors->has('secret_access_key'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('secret_access_key') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Add Credentials
                    </button>

                    <a class="btn btn-link" href="{{ url('credentials/guide') }}">
                        Need Help Adding Credentials?
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>