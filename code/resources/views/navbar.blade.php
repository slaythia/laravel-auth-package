<nav class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{!! asset('/images/brand.png') !!}" width="180" height="40">
        </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
            @if (Auth::guard()->check())
                <li class="no-hover"><img class="navbar-brand img-circle" src="
                @if (Auth::user()->avatar)
                    {{ Auth::user()->avatar }}
                    @else
                            images/avatar.png
                            @endif
                            "></li>
                <li class="no-hover"><p class="navbar-text">{{ $navBarHi }}, {{ Auth::user()->name }}</p></li>
                @if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <li><a href="{{ url('/admin') }}"><i class="material-icons">
                                &#xE31E;</i>&nbsp;{{ $navBarAdmin }}</a></li>
                @endif
                <li><a href="{{ url('logout') }}"><i class="material-icons">&#xE879;</i>&nbsp;{{ $navBarLogOut }}
                    </a></li>
            @else
                <li><a href="{{ url('login') }}"><i class="material-icons">&#xE7FF;</i>&nbsp;{{ $navBarLogIn }}
                    </a></li>
            @endif
        </ul>
    </div>
</nav>

