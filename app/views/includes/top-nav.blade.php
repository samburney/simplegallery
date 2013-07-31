<ul id="top-nav" class="nav nav-pills row">
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
</ul>
