@extends ('layouts.main')

@section ('content')
<div class="row-fluid">
	<h2 class="text-center">User Login</h2>
	<div class="offset2 span8 well">
	{{Former::open()}}
	{{Former::text('username', 'Username or Email')}}
	{{Former::password('password')}}
	<div class="text-center">{{Former::submit('Login')}}</div>
	{{Former::close()}}
	</div>
</div>
@endsection