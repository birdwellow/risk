@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">{{ Lang::get('message.title.register') }}</div>
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

					<form class="form-horizontal" role="form" method="POST" action="/auth/register">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_name') }}</label>
							<div class="col-md-6">
								<input type="text" class="{{ invalid("new_user_name") }} form-control" name="new_user_name" value="{{ old('new_user_name') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_email') }}</label>
							<div class="col-md-6">
								<input type="email" class="{{ invalid("new_user_email") }} form-control" name="new_user_email" value="{{ old('new_user_email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_password') }}</label>
							<div class="col-md-6">
								<input type="password" class="{{ invalid("new_user_password") }} form-control" name="new_user_password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ Lang::get('input.new_user_password_confirmation') }}</label>
							<div class="col-md-6">
								<input type="password" class="{{ invalid("new_user_password_confirmation") }} form-control" name="new_user_password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									{{ Lang::get('message.button.register') }}
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
