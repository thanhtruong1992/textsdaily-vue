
@extends('layouts.app')

@section('title', 'Short link')

@section('script')
<script>
    $(document).ready(function(){
        var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };
        var phone = getUrlParameter('phone');
        var first_name = getUrlParameter('first_name');
        var last_name = getUrlParameter('last_name');
        var email = getUrlParameter('email');
        $('#phone').val(phone);
        $('#first_name').val(first_name);
        $('#last_name').val(last_name);
        $('#email').val(email);
    });

</script>

@endsection
@section('content')
<div class="container-fluid" style="">
<form style="width:500px; margin: auto; margin-top: 100px;">
  <div class="form-group">
    <label for="exampleInputEmail1">Email </label>
    <input type="email" class="form-control" id="email" placeholder="Email">
    
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">First name</label>
    <input type="text" class="form-control" id="first_name" placeholder="First name">
  </div>
  <div class="form-group">
    <label for="last_name">Last name </label>
    <input type="text" class="form-control" id="last_name" placeholder="Last name ">
    
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Phone </label>
    <input type="text" class="form-control" id="phone" placeholder="Phone">
  </div>
</form>
</div>
@endsection
