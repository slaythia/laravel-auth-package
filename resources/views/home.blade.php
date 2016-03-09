
@extends('app')

@section('content')
    <div class="title">Home</div>
    <h2>EpiCollect5</h2>
    <div class="form-group">
        @foreach ($methods as $method)
            <a class="btn @if( $method['provider'] != 'google')btn-default @endif" href="{{ $method['url'] }}">{{ $method['button_text'] }}
                @if( $method['provider'] == 'google')
                    <img class="img-responsive" src="/PHE/EpiCollectplus/images/gplus-signin.png" width="310">
                @endif
            </a>
        @endforeach
        <h1>Latest Projects</h1>
        <table class="table table-striped table-bordered">
            <tr><th>Name</th><th>Description</th></tr>
            <tr><td>{{ $project->getName() }}</td><td>{{ $project->getDescription() }}</td></tr>
        </table>
    </div>

@stop