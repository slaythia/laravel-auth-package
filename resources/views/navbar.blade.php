<nav class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}"><img src="{!! asset('/images/brand.png') !!}"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::guard()->check())
                        <li><img class="navbar-brand img-circle" src="{{ Auth::user()->avatar }}"></li>
                        <li class="ec-username">Hi, {{ Auth::user()->name }}</li></li>
                            @if (Auth::user()->server_role != 'basic')
                            <li><a href="{{ url('/admin') }}">Admin</a></li>
                            @endif
                        <li><a href="{{ url('logout') }}">Logout</a></li>
                    @else
                        <li><a href="{{ url('login') }}">Login</a></li>
                    @endif
                </ul>
            </div>
    </nav>