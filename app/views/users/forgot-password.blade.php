@extends ('layouts.main')

@section('page_title')
	// Send Password Reset Email
@endsection

@section ('content')
<div class="row">
	<h2 class="text-center">Send Password Reset Email</h2>
	<div class="col-lg-offset-2 col-lg-8 col-sm-offset-2 col-sm-8 well">
		{{Former::framework('Nude')}}
		{{Former::open()
			->rules(array(
				'username' => 'required|min:2|max:32|alpha_num',
				'email' => 'required|email',
			))
			->class('form-horizontal')
		}}
		<div class="form-group">
			{{Former::label('Username', 'username')->class('col-lg-4 col-sm-4 control-label')}}
			<div class="col-lg-8 col-sm-8">
				{{Former::text('username')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="form-group">
			{{Former::label('Email', 'email')->class('col-lg-4 col-sm-4 control-label')}}
			<div class="col-lg-8 col-sm-8">
				{{Former::text('email')->class('form-control')->label(false)}}
			</div>
		</div>
		<div class="text-center">
			{{Former::submit('Send Email')->class('btn btn-primary')}}
		</div>
		{{Former::close()}}
	</div>
</div>
@endsection