<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
        
	<title>{{ Lang::get('message.title') }}</title>
        
	<link href="/css/bootstrap.css" rel="stylesheet">
	<link href="/css/bootstrap-theme.css" rel="stylesheet">
	<link href="/css/jquery-ui.css" rel="stylesheet">
	<link href="/css/common.css" rel="stylesheet">
	<link href="/css/-DEF.css" rel="stylesheet">

        <script src="/js/thirdparty/jquery.min.js" defer="defer"></script>
        <script src="/js/thirdparty/jquery-ui.min.js" defer="defer"></script>
        <script src="/js/thirdparty/bootstrap.min.js" defer="defer"></script>
        <script src="/js/app.js" defer="defer"></script>
        
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>  
        
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand">
                                        Risiko
                                </a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li>
                                                <a href="{{ route('index') }}">
                                                        <img class="icon" src="/img/home.png">
                                                        {{ Lang::get('message.link.home') }}
                                                </a>
                                        </li>
                                        @if (Auth::check())
                                        <li class="dropdown">
                                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                        <img class="icon" src="/img/matches.png">
                                                        {{ Lang::get('message.title.matches') }}
                                                        <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu" role="menu">
                                                        
                                                        @if(!Auth::user()->joinedMatch)
                                                        <li>
                                                                <a href="{{ route('match.new') }}">
                                                                        {{ Lang::get('message.link.match.new') }}
                                                                </a>
                                                        </li>
                                                        @else
                                                        <li>
                                                                <a class="inactive" onclick="UI.error('{{ Lang::get("error.user.already.joined") }}', '{{ Lang::get("error.user") }}', 'OK')">
                                                                        {{ Lang::get('message.link.match.new') }}
                                                                </a>
                                                        </li>
                                                        @endif
                                                        
                                                        @if(Auth::user()->createdMatch)
                                                        <li>
                                                                <a href="{{ route('match.administrate', Auth::user()->createdMatch->id) }}">
                                                                        {{ Lang::get('message.link.match.administrate', ['matchName'=>Auth::user()->createdMatch->name]) }}
                                                                </a>
                                                        </li>
                                                        @endif
                                                </ul>
                                        </li>
                                        
                                        <li class="dropdown">
                                            
                                                <?php
                                                    $newMessagesCount = Auth::user()->newMessagesCount();
                                                    $class = ($newMessagesCount ? "newmessages " : "");
                                                ?>
                                                <a class="{{ $class }}dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                        <img class="icon" src="/img/message.png">
                                                        @if($newMessagesCount)
                                                            <span class="newmessagescount">
                                                            {{ $newMessagesCount }}
                                                            </span>
                                                        @endif
                                                        {{ Lang::get('message.title.messages') }}
                                                        <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                                <a href="{{ route('all.threads') }}">
                                                                        {{ Lang::get('message.title.all.messages') }}
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="{{ route('new.thread.init') }}">
                                                                        {{ Lang::get('message.title.new.thread') }}
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        @endif
				</ul>

				<ul class="nav navbar-nav navbar-right">
                                        
                                        <li class="dropdown">
                                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                        {{ Lang::get("message.name.language." . App::getLocale()) }}
                                                        <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                                <a href="{{ route('switch.language', "en") }}">
                                                                        {{ Lang::get('message.name.language.en') }}
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="{{ route('switch.language', "de") }}">
                                                                        {{ Lang::get('message.name.language.de') }}
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        
					@if (Auth::guest())
						<li>
                                                        <a href="/auth/login">
                                                                {{ Lang::get('message.link.login') }}
                                                        </a>
                                                </li>
						<li>
                                                        <a href="/auth/register">
                                                                {{ Lang::get('message.link.register') }}
                                                        </a>
                                                </li>
					@else
						<li class="dropdown">
                                                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                            @if( Auth::user()->avatarfile )
                                                                <img class="user-avatar" src="/img/avatars/{{ Auth::user()->avatarfile }}" />
                                                            @else
                                                                <img class="user-avatar" src="/img/avatars/default.png">
                                                            @endif
                                                            {{ Auth::user()->name }}
                                                            <span class="caret"></span>
                                                        </a>
							<ul class="dropdown-menu" role="menu">
								<li>
                                                                        <a href="{{ route('user.profile') }}">
                                                                            <img class="icon-s" src="/img/profile-s.png">
                                                                            {{ Lang::get('message.link.profile') }}
                                                                        </a>
                                                                </li>
								<li>
                                                                        <a href="{{ route('user.options') }}">
                                                                            <img class="icon-s" src="/img/options-s.png">
                                                                            {{ Lang::get('message.link.options') }}
                                                                        </a>
                                                                </li>
								<li class="logout">
                                                                        <a href="/auth/logout">
                                                                            <img class="icon-s" src="/img/logout-s.png">
                                                                            {{ Lang::get('message.link.logout') }}
                                                                        </a>
                                                                </li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
                    
		</div>
            
	</nav>
    

        @if (session()->has('message') || isset($message))
        
        <?php $message = ( session('message') ? session('message') : $message); ?>
        
        <div class="container">
                <div class="col-md-10 col-md-offset-1 alert alert-{{ $message->type }}">
                        {{ Lang::get($message->messageKey) }}
                        @if (isset($message->hints) && $message->hints)
                            <ul>
                                @foreach ($message->hints->all() as $hints)
                                    <li>
                                            {{ $hints }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                </div>
        </div>
        @endif

	@yield('content')

</body>
</html>
