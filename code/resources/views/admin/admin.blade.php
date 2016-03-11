@extends('app')


@section('content')
    <div class="container">
        <h2 class="page-title">{{ trans('site.admin')}}</h2>

        {{-- Error handling --}}
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ trans('status_codes.' . $error) }}
            </div>
        @endforeach

        {{-- Success Message --}}
        @if (session('message'))
        <div class="var-holder-success" data-message="{{trans('status_codes.'.session('message'))}}"></div>
        <script>
            //Display a success toast, with a title
            toastr.options={
                closeButton: true,
                positionClass: 'toast-top-center',
                preventDuplicates: true,
                onclick: null,
                showDuration: 500,
                hideDuration: 500,
                timeOut: 3000,
                extendedTimeOut:0,
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };
            toastr.success($('.var-holder-success').attr('data-message'));
        </script>
        @endif


        {{-- Nav tabs --}}
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#user-administration" aria-controls="user-administration"
                                                      role="tab" data-toggle="tab">User Administration</a></li>
        </ul>

        {{-- Tab panes --}}
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active user-administration" id="user-administration">
                <div class="row">

                    {{-- All Users --}}

                    <div class="col-lg-9 col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <input type="text" name="search"
                                               class="form-control user-administration__user-search"
                                               placeholder="Search for User">
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <select name="filter"
                                                        class="form-control user-administration__user-filter">
                                                    <option value="">-- Filter By --</option>
                                                    <option value="state">State</option>
                                                    <option value="server_role">Access</option>
                                                </select>
                                            </div>
                                            <div class="col-xs-6">
                                                <select name="filteroption"
                                                        class="form-control user-administration__user-filter-option"
                                                        disabled>
                                                    <option value="">-- Option --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-2">
                                        <a class="btn btn-danger user-administration__user-reset pull-right" href="">Reset
                                            Table</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                {{-- Include Users view --}}
                                <div class="user-administration__users">
                                    @include('admin.users')
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Add new User form --}}

                    <div class="col-lg-3 col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Add New Local User
                            </div>
                            <div class="panel-body">

                                <form method="POST" action="{{ url('admin/register-by-admin') }}" accept-charset="UTF-8"
                                      class="form">

                                    {!! csrf_field() !!}

                                    <div class="form-group @if ($errors->has('name')) has-error @endif">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Full Name"
                                               name="name" value="{{ old('name') }}" required>
                                        @if ($errors->has('name')) <span
                                                class="help-block">{{ $errors->first('name') }}</span> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('email')) has-error @endif">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="Email Address"
                                               name="email" value="{{ old('email') }}" required>
                                        @if ($errors->has('email')) <span
                                                class="help-block">{{ $errors->first('email') }}</span> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="Password"
                                               name="password" value="{{ old('password') }}" required>
                                        @if ($errors->has('password')) <span
                                                class="help-block">{{ $errors->first('password') }}</span> @endif
                                    </div>

                                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                                        <label for="password_confirmation">Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                               placeholder="Confirm Password" name="password_confirmation"
                                               value="{{ old('password_confirmation') }}" required>
                                        @if ($errors->has('password')) <span
                                                class="help-block">{{ $errors->first('password') }}</span> @endif
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" class="btn btn-default btn-action pull-right"
                                               value="Add User">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    {{--<script src="{!! asset('/js/jquery.autocomplete.min.js') !!}"></script>--}}
    <script src="{!! asset('/js/admin/users.js') !!}"></script>
@stop