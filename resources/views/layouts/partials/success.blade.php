@if(Session::has('success') > 0)
<div id="close" class="alerts-success">
<a><span class="fe fe-thumbs-up"></span> {{Session::get('success')}}</a><br>
</div>
@else
<div id="close" class="csp_style1"></div>
@endif
