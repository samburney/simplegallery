@extends ('layouts.main')

@section('page_title')
	// User Registration
@endsection

@section ('content')
<div class="row-fluid">
	<h2 class="text-center">User Registration</h2>
	<div class="col-offset-2 col-lg-8 well">
		{{Former::framework('Nude')}}
		{{Former::open()
			->rules(array(
				'username' => 'required|min:2|max:32|alpha_num',
				'email' => 'required|email',
				'password' => 'required',
				'password_confirm' => 'required'
			))
			->class('form-horizontal')
		}}
		<div class="form-group">
			{{Former::label('Username', 'username')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::text('username')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Email', 'email')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::text('email')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Password', 'password')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::password('password')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Confirm Password', 'password')->class('col-lg-4 control-label')}}
			<div class="col-lg-8">
				{{Former::password('password_confirm')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="text-center">
			{{Former::submit('Register')->class('btn btn-primary')}}
		</div>
		{{Former::close()}}
	</div>
</div>
@endsection