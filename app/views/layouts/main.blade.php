<!DOCTYPE html>
<html lang="en">
	<head>
		<title>sifnt Upload</title>
@section('scripts')
		<script type="text/javascript" src="/js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="/js/jquery.scrollto.js"></script>
		<script type="text/javascript" src="/lib/jquery.fineuploader-3.6.4/jquery.fineuploader-3.6.4.js"></script>
		<script type="text/javascript" src="/lib/bootstrap-2.3.2/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/js/bootbox.min.js"></script>
		<script type="text/javascript" src="/lib/select2-3.4.1/select2.js"></script>
@show
@section('styles')
		<link href="/lib/jquery.fineuploader-3.6.4/fineuploader-3.6.4.css" rel="stylesheet" media="all">
		<link href="/lib/bootstrap-2.3.2/css/bootstrap.css" rel="stylesheet" media="all">
		<link href="/lib/select2-3.4.1/select2.css" rel="stylesheet" media="all">
		<link href="/css/style.css" rel="stylesheet" media="all">
@show
	</head>
	<body>
		<div class="container">
@section('topnav')
			<div id="topnav" class="navbar navbar-inverse">
				<div class="navbar-inner">
					<a class="brand" href="/">sU</a>
@if (@$user)
					<ul class="nav pull-right">
						<li class="divider-vertical"></li>
						<li class="dropdown">
							<a href="#" class="dropdown" data-toggle="dropdown">
								<i class="icon-user icon-white"></i>
	@if ($user->id > 0)
								{{$user->username}}
	@else
								Not Logged In
	@endif
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
	@if ($user->id <=0 || ($user->id > 0 && !$user->email && !$user->password) || !Auth::check())
								<li><a href="/user/login">Log in</a></li>
	@endif
	@if ($user->id <= 0 || !$user->email || !$user->password)
								<li><a href="/user/register">Register</a></li>
	@endif	
	@if ($user->id > 0)
								<li><a href="/user/logout">Log out</a></li>
	@endif
							</ul>
						</li>
					</ul>
@endif
				</div>
			</div>
@show
@if (Session::has('error'))
			<div class="alert alert-error">
				<b>Error!</b> {{Session::get('error')}}
			</div>
@endif
@if (Session::has('warning'))
			<div class="alert alert-warning">
				<b>Warning:</b> {{Session::get('warning')}}
			</div>
@endif
@if (Session::has('notice'))
			<div class="alert alert-success">
				<b>Notice:</b> {{Session::get('notice')}}
			</div>
@endif
		</div>
		<div class="container">
			@yield('content')
		</div>
	</body>
</html>