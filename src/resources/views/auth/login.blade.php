@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">{{ Lang::get('message.title.login') }}</div>
				<div class="panel-body">

					<form class="form-horizontal" role="form" method="POST" action="/auth/login">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.user_email') }}</label>
							<div class="col-md-6">
								<input type="text" class="form-control {{ invalid("user_email") }}" name="user_email" value="{{ old('user_email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.user_password') }}</label>
							<div class="col-md-6">
								<input type="password" class="form-control {{ invalid("user_password") }}" name="user_password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
                                                                        <input type="checkbox" name="user_remember_login" id="user_remember_login">
                                                                        <label for="user_remember_login">
                                                                            {{ Lang::get('input.user_remember_login') }}
                                                                        </label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									{{ Lang::get('message.button.login') }}
								</button>

								<a href="/password/email">{{ Lang::get('message.link.password.forgotten') }}</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
