@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">{{ Lang::get('message.title.password.reset') }}</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							{{ Lang::get('message.error.userinput') }}<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="/password/reset">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="token" value="{{ $token }}">

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.user_email') }}</label>
							<div class="col-md-6">
								<input type="email" class="{{ invalid('user_email') }} form-control" name="user_email" value="{{ old('user_email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_password') }}</label>
							<div class="col-md-6">
								<input type="password" class="{{ invalid('new_user_password') }} form-control" name="new_user_password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_password_confirmation') }}</label>
							<div class="col-md-6">
								<input type="password" class="{{ invalid('new_user_password_confirmation') }} form-control" name="new_user_password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									{{ Lang::get('message.button.password.reset') }}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
