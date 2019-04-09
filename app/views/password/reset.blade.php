@extends ('layouts.main')

@section('page_title')
	// Send Password Reset Email
@endsection

@section ('content')
<div class="row">
	<h2 class="text-center">Password Reset</h2>
	<div class="col-lg-offset-2 col-lg-8 col-sm-offset-2 col-sm-8 well">
		{{Former::framework('Nude')}}
		{{Former::open()
			->rules(array(
				'password' => 'required',
				'password_confirmation' => 'required'
			))
			->class('form-horizontal')
		}}
        <input type="hidden" name="token" value="{{ $token }}">
		<div class="form-group">
			{{Former::label('Email', 'email')->class('col-lg-4 col-sm-4 control-label')}}
			<div class="col-lg-8 col-sm-8">
				{{Former::text('email')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Password', 'password')->class('col-lg-4 col-sm-4 control-label')}}
			<div class="col-lg-8 col-sm-8">
				{{Former::password('password')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Confirm Password', 'password')->class('col-lg-4 col-sm-4 control-label')}}
			<div class="col-lg-8 col-sm-8">
				{{Former::password('password_confirmation')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="text-center">
			{{Former::submit('Change Password')->class('btn btn-primary')}}
		</div>
		{{Former::close()}}
	</div>
</div>
@endsection