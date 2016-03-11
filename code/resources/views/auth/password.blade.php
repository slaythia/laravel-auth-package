
@extends('app')

@section('content')
    <div class="title">Forgot Password</div>

    @foreach($errors->all() as $error)
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ trans('status_codes.' . $error) }}
        </div>
    @endforeach

    {{-- Success Message/Status --}}
    @if (session('status'))
        <div class="var-holder-success" data-message="{{ session('status') }}"></div>
        <script>
            //Display a success toast, with a title
            toastr.options = {
                closeButton: true,
                positionClass: 'toast-top-center',
                preventDuplicates: true,
                onclick: null,
                showDuration: 500,
                hideDuration: 500,
                timeOut: 3000,
                extendedTimeOut: 0,
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };
            toastr.success($('.var-holder-success').attr('data-message'));
        </script>
    @endif

    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">

                    {!! Form::open(array('url' => url('password'))) !!}

                        {!! csrf_field() !!}

                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Email Address']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Send Reminder', ['class' => 'btn btn-default']) !!}
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@stop