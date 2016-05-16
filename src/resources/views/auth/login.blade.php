@extends('app')

@section('content')

<div class="welcome">
    <div class="title">
        {{ Lang::get("message.title.welcome") }}
    </div>
    <img src="/img/logo.png">
    <div>
        <button class="login-link btn btn-primary">
                {{ Lang::get('message.button.login') }}
        </button>
        <div class="links">
            <a href="#" id="tour-link">
                    {{ Lang::get('message.title.tour') }}
            </a>
            <a href="/auth/register">
                    {{ Lang::get('message.link.register') }}
            </a>
        </div>
    </div>
</div>

<img src="/img/world.png" class="background-img welcome"/>

<div id="tour-carousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#tour-carousel" data-slide-to="0" class="active"></li>
        <li data-target="#tour-carousel" data-slide-to="1"></li>
        <li data-target="#tour-carousel" data-slide-to="2"></li>
        <li data-target="#tour-carousel" data-slide-to="3"></li>
    </ol>

    <div class="carousel-inner" role="listbox">
        <div class="item active">
            <div class="carousel-caption">
                {{ Lang::get('message.tour.caption.map') }}
            </div>
            <img src="/img/tour/1.png">
        </div>

        <div class="item">
            <div class="carousel-caption">
                {{ Lang::get('message.tour.caption.conquer') }}
            </div>
            <img src="/img/tour/2.png">
        </div>

        <div class="item">
            <div class="carousel-caption">
                {{ Lang::get('message.tour.caption.enforments') }}
            </div>
            <img src="/img/tour/3.png">
        </div>

        <div class="item">
            <div class="carousel-caption">
                {{ Lang::get('message.tour.caption.communicate') }}
            </div>
            <img src="/img/tour/4.png">
        </div>
    </div>

    <a class="left carousel-control" href="#tour-carousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">
            Previous
        </span>
    </a>
    <a class="right carousel-control" href="#tour-carousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">
            Next
        </span>
    </a>
</div>

<a name="login"></a>
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
                                                            
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                                                                {{ Lang::get('message.button.login') }}
                                                                        </button>
                                                                    </td>
                                                                    
                                                                    <td>
                                                                        <a href="/password/email">
                                                                                {{ Lang::get('message.link.password.forgotten') }}
                                                                        </a>

                                                                        <br>

                                                                        <a href="/auth/register">
                                                                                {{ Lang::get('message.link.register') }}
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
