<!DOCTYPE html>
<html lang="en">
	<head>
		<title>sifnt Upload</title>
@section('scripts')
		<script type="text/javascript" src="/js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="/lib/jquery.fineuploader-3.6.4/jquery.fineuploader-3.6.4.js"></script>
		<script type="text/javascript" src="/lib/bootstrap-2.3.2/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/js/bootbox.min.js"></script>
@show
@section('styles')
		<link href="/lib/jquery.fineuploader-3.6.4/fineuploader-3.6.4.css" rel="stylesheet" media="all">
		<link href="/lib/bootstrap-2.3.2/css/bootstrap.css" rel="stylesheet" media="all">
		<link href="/css/style.css" rel="stylesheet" media="all">
@show
	</head>
	<body>
		<div class="container">
@section('topnav')
			<div id="topnav" class="navbar navbar-inverse">
				<div class="navbar-inner">
					<a class="brand" href="/">sU</a>
					<ul class="nav pull-right">
						<li class="divider-vertical"></li>
						<li><a href="/upload"><i class="icon-user icon-white"></i>
@if ($user && $user->id > 0)
							{{$user->username}}
@else
							Not Logged In
@endif
						</a></li>
					</ul>
				</div>
			</div>
@show
@if (Session::has('error'))
			<div class="alert alert-error">
				<b>Error!</b> {{Session::get('error')}}
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