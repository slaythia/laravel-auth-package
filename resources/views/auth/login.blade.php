
@extends('app')

@section('content')
    <div class="title">Login</div>

    @foreach($errors->all() as $error)
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ trans('status_codes.' . $error) }}
        </div>
    @endforeach

    {{-- Success Message --}}
    @if (session('message'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ trans('status_codes.' . session('message')) }}
        </div>
    @endif

    <div class="row">

        @if (in_array('local', $authMethods))
        <div class="col-lg-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">

                    <form method="POST" action="{{ url('login') }}" accept-charset="UTF-8">

                        {!! csrf_field() !!}

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" required="required" placeholder="Email Address" name="email" type="email" id="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" required="required" placeholder="Password" name="password" type="password" value="" id="password">
                        </div>
                        <div class="form-group">
                            <label for="remember">Remember Me</label>
                            <input name="remember" type="checkbox" value="1" id="remember">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-default" type="submit" value="Login">
                        </div>

                    </form>
                    <div class="form-group">
                        <a href="{{url('password')}}">Forgot Password?</a>
                    </div>

                </div>
            </div>
        </div>
        @endif
        @if (in_array('google', $authMethods))
            <div class="col-lg-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <a class="btn full-width" href="{{ url('redirect/google') }}">
                            <img class="img-responsive center" src="{!! asset('/images/gplus-signin.png') !!}" width="310">
                        </a>

                    </div>
                </div>
            </div>
        @endif
        @if (in_array('ldap', $authMethods))
            <div class="col-lg-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form method="POST" action="{{ url('login/ldap') }}" accept-charset="UTF-8">

                            {!! csrf_field() !!}

                            <div class="form-group">
                                <label for="email">Ldap Username</label>
                                <input class="form-control" required="required" placeholder="Email Address" name="username" type="text">
                            </div>
                            <div class="form-group">
                                <label for="password">Ldap Password</label>
                                <input class="form-control" required="required" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <div class="form-group">
                                <input class="btn btn-default" type="submit" value="Ldap Login">
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        @endif

    </div>

@stop