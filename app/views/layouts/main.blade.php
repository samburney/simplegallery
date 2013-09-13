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
		<script type="text/javascript" src="{{asset('bower/jquery/jquery.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('bower/jquery.smooth-scroll/jquery.smooth-scroll.js')}}"></script>
		<script type="text/javascript" src="{{asset('lib/jquery.fineuploader-3.8.0/jquery.fineuploader-3.8.0.js')}}"></script>
		<script type="text/javascript" src="{{asset('bower/fancybox/lib/jquery.mousewheel-3.0.6.pack.js')}}"></script>
		<script type="text/javascript" src="{{asset('bower/fancybox/source/jquery.fancybox.pack.js?v=2.1.5')}}"></script>
		<script type="text/javascript" src="{{asset('bower/bootstrap/dist/js/bootstrap.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('bower/bootbox/bootbox.js')}}"></script>
		<script type="text/javascript" src="{{asset('bower/select2/select2.js')}}"></script>
		<script type="text/javascript">
			var baseURL = "{{URL::to('')}}";
			var Session = {{json_encode(Session::all())}}

			$(function() {
				if(Session.width != $(window).width() || Session.height != $(window).height()) {
					$.post(
						baseURL + '/upload/set-client-session-vars',
						{
							height: $(window).height(),
							width: $(window).width()
						}
					);
				}
			})
		</script>
@show
@section('styles')
		<link href="{{asset('lib/jquery.fineuploader-3.8.0/fineuploader-3.8.0.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('bower/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('bower/select2/select2.css')}}" rel="stylesheet" media="screen">
		<link href="{{asset('bower/fancybox/source/jquery.fancybox.css?v=2.1.5')}}" rel="stylesheet" media="screen">
		<link href="{{asset('css/style.css')}}" rel="stylesheet" media="screen">
@show
	</head>
	<body>
		<div id="wrap">
			<div class="container" id="header">
@section('title-nav')
				<div id="title-nav" class="row">
					<nav class="navbar navbar-inverse" role="navigation">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-topnav-toggle">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a href="{{URL::to('upload/upload')}}" class="btn btn-primary navbar-btn visible-xs pull-right">Upload</button>
							<a class="navbar-brand" href="{{URL::route('home')}}">sU</a>
						</div>
						<div class="navbar-collapse navbar-topnav-toggle collapse">
							<ul class="nav navbar-nav">
								<li<? if(Request::is('/') || Request::is('/uploads/popular')){ ?> class="active"<? } ?>>
									<a href="{{URL::route('home')}}">Popular</a>
								</li>
								<li<? if(Request::is('tag') || Request::is('tags')){ ?> class="active"<? } ?>>
									<a href="{{URL::to('tags')}}">Tags</a>
								</li>
							@if(Auth::user()->id > 0)
								<li<? if(Request::is('uploads')){ ?> class="active"<? } ?>>
									<a href="{{URL::to('uploads')}}">Your Uploads</a>
								</li>
								<li<? if(Request::is('collection') || Request::is('collections')){ ?> class="active"<? } ?>>
									<a href="{{URL::to('collections')}}">Your Collections</a>
								</li>
							@endif
								<li<? if(Request::is('search') || Request::is('search')){ ?> class="active"<? } ?>>
									<a href="{{URL::to('search')}}">Search</a>
								</li>
								<li>
									<button type="button" class="btn btn-primary navbar-btn visible-sm" onclick="window.location='{{URL::to('upload/upload')}}'">Upload</button>
								</li>
							</ul>
							<ul class="nav navbar-nav navbar-right">
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
										<li>
											<a href="{{URL::to('uploads')}}">Uploads</a>
										</li>
										<li>
											<a href="{{URL::to('collections')}}">Collections</a>
										</li>
										<li><a href="{{URL::to('user/logout')}}">Log out</a></li>
@endif
									</ul>
								</li>
							</ul>
						</div>
					</nav>
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
			<div id="content" class="container">
				@yield('content')
			</div>
			<div id="push">
			</div>
		</div>
		<div class="container" id="footer">
			<div class="row text-center">
				<p>
					<small>
						<a href="http://code.sifnt.wan/tanuki/simplegallery/issues">Report a Bug or Request a Feature</a><br>
						SimpleGallery is Copyright &copy;2013 <a href="http://www.sifnt.net.au/">sifnt</a>.  Use of this software is licensed under the General Public License (GPL) version 2.
					</small>
				</p>
			</div>
		</div>
	</body>
</html>