<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			sifnt Upload
			@section('page_title')
			@show
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
@section('scripts')
		<script type="text/javascript">
			var baseURL = "{{URL::to('')}}";
		</script>
		<script type="text/javascript" src="{{asset('js/jquery-1.10.1.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('js/jquery.scrollto.js')}}"></script>
		<script type="text/javascript" src="{{asset('lib/jquery.fineuploader-3.6.4/jquery.fineuploader-3.6.4.js')}}"></script>
		<script type="text/javascript" src="{{asset('lib/bootstrap-3.0.0-rc1/js/bootstrap.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('js/bootbox.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('lib/select2-3.4.1/select2.js')}}"></script>
@show
@section('styles')
		<link href="{{asset('lib/jquery.fineuploader-3.6.4/fineuploader-3.6.4.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('lib/bootstrap-3.0.0-rc1/css/bootstrap.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('lib/bootstrap-glyphicons-1.0.0/css/bootstrap-glyphicons.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('lib/select2-3.4.1/select2.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('css/style.css')}}" rel="stylesheet" media="screen">
@show
	</head>
	<body>
		<div class="container">
@section('title-nav')
			<div class="row">
				<div id="title-nav" class="navbar navbar-inverse">
					<a class="navbar-brand" href="{{URL::route('home')}}">sU</a>
@if (@$user)
					<ul class="nav navbar-nav pull-right">
						<li class="divider-vertical"></li>
						<li class="dropdown">
							<a href="#" class="dropdown" data-toggle="dropdown">
								<i class="glyphicon glyphicon-user glyphicon-white"></i>
@if ($user->id > 0)
								{{$user->username}}
@else
								Not Logged In
@endif
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
@if ($user->id <=0 || ($user->id > 0 && !$user->email && !$user->password) || !Auth::check())
							<li><a href="{{URL::to('user/login')}}">Log in</a></li>
@endif
@if ($user->id <= 0 || !$user->email || !$user->password)
							<li><a href="{{URL::to('user/register')}}">Register</a></li>
@endif	
@if ($user->id > 0)
							<li><a href="{{URL::to('user/logout')}}">Log out</a></li>
@endif
						</ul>
					</li>
				</ul>
@endif
			</div>
		</div>
@show
@if (Session::has('error'))
			<div class="alert alert-error row">
				<b>Error!</b> {{Session::get('error')}}
			</div>
@endif
@if (Session::has('warning'))
			<div class="alert alert-warning row">
				<b>Warning:</b> {{Session::get('warning')}}
			</div>
@endif
@if (Session::has('notice'))
			<div class="alert alert-success row">
				<b>Notice:</b> {{Session::get('notice')}}
			</div>
@endif
		</div>
		<div class="container">
			@yield('content')
		</div>
	</body>
</html>