@if($errors->count() > 0)
<div id="close" class="alerts">
@foreach($errors->all() as $error)
<a>{{$error}}</a><br>
@endforeach
</div>
@else
<div id="close" class="csp_style1"></div>
@endif

