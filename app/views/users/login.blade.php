@extends ('layouts.main')

@section('page_title')
	// User Login
@endsection

@section ('content')
<div class="row">
	<h2 class="text-center">User Login</h2>
	<div class="col-offset-2 col-lg-8 well">
		{{Former::framework('Nude')}}
		{{Former::open()
			->class('form-horizontal')
			->rules(array(
				'username' => 'required|min:2|max:32',
				'password' => 'required',
			))
		}}
		<div class="form-group">
			{{Former::label('Username or Email', 'username')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::text('username')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Password', 'password')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::password('password')->class('form-control')->label(false)}}
				<div class="checkbox">
					{{Former::checkbox('remember', false)}}
					Remember me
				</div>
			</div>
		</div>
		<div class="text-center">{{Former::submit('Login')->class('btn btn-primary')}}</div>
		{{Former::close()}}
	</div>
</div>
@endsection