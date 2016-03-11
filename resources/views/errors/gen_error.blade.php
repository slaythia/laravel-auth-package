@extends('app')

@section('content')

<h2>General Error</h2>

@if ($errors->has())
    <div class="alert alert-danger">
    <p>There has been an Error!</p>
 
    @foreach($errors->getMessages() as $key => $error)
         @foreach($error as $key2 => $error2)
        <p>{{ $key }} {{ $error2 }} </p>
      @endforeach
    @endforeach
    
    </div>
@endif

@stop