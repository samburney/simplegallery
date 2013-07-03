@extends ('layouts.main')

@section ('content')
<div class="row-fluid">
	<h2 class="text-center">User Registration</h2>
	<div class="offset2 span8 well">
		{{Former::open()
			->rules(array(
				'username' => 'required|min:2|max:32|alpha_num',
				'email' => 'required|email',
				'password' => 'required',
				'password_confirm' => 'required'
			)
		)}}
		{{Former::text('username')}}
		{{Former::text('email')}}
		{{Former::password('password')}}
		{{Former::password('password_confirm', 'Confirm Password')}}
		<div class="text-center">
			{{Former::submit('Register')}}
		</div>
		{{Former::close()}}
	</div>
</div>
@endsection