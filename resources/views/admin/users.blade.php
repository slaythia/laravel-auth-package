
@if (count($users) == 0) <p class="well">{{ trans('site.no_users_found')}}</p>
@else
    <table class="table table-bordered user-administration__table">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>State</th>
            <th>Access</th>
        </tr>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form method="POST" action="{{ url('/admin/update-user-state') }}" accept-charset="UTF-8" class="user-administration__table__state-form">

                        {!! csrf_field() !!}

                        <input type="hidden" name="email" value="{{ $user->email }}">
                        @if ($user->state == 'active' && $user->server_role != 'superadmin')
                            <span>Active</span>
                            <input type="hidden" name="state" value="disabled">
                            <div class="form-group">
                                <input type="submit" class="btn btn-xs btn-danger user-administration__table__state-submit--disable" value="Disable">
                            </div>
                        @elseif ($user->server_role == 'superadmin')
                            <span><i>A Super Admin cannot be disabled</i></span>
                        @else
                            <input type="hidden" name="state" value="active">
                            <span>Disabled</span>
                            <div class="form-group">
                                <input type="submit" class="btn btn-xs btn-action user-administration__table__state-submit--activate" value="Activate">
                            </div>
                        @endif
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ url('/admin/update-user-server-role') }}" accept-charset="UTF-8" class="user-administration__table__server-role-form">

                        {!! csrf_field() !!}

                        <input type="hidden" name="email" value="{{ $user->email }}">
                        @if ($user->server_role == 'admin')
                            {!! Form::hidden('server_role', 'basic') !!}
                            <span>Admin</span>
                            <div class="form-group">
                                <input type="submit" class="btn btn-xs btn-danger user-administration__table__server-role-submit--remove-as-admin" value="Remove as Admin">
                            </div>
                        @elseif ($user->server_role == 'basic')
                            <input type="hidden" name="server_role">{!! Form::hidden('server_role', 'admin') !!}
                            <span>Basic</span>
                            <div class="form-group">
                                <input type="submit" class="btn btn-xs btn-action user-administration__table__server-role-submit--make-admin" value="Make Admin">
                            </div>
                        @else <span>Super Admin</span>
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    {!! $users->render() !!}
@endif