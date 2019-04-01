@if($errors->count() > 0)
<div id="close" class="alerts">
@foreach($errors->all() as $error)
<a>{{$error}}</a><br>
@endforeach
</div>
@else
<div id="close" style="display: none"></div>
@endif

