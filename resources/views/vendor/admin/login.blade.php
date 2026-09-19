{{--
    Custom admin login for Ndege Estate branches.
    Overrides vendor/encore/laravel-admin login. Branch name comes from
    config('app.name') so every branch is styled identically but self-labelled.
    Asset paths are docroot-relative (the docroot IS Laravel's public dir).
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} | {{ trans('admin.login') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if (!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif

    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css') }}">
    <style>
        :root {
            --green: #0f5132;
            --green-2: #157347;
        }

        body.hold-transition.login-page {
            background: #eef1ee;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 380px;
            max-width: 92vw;
            margin: 0;
        }

        .brand {
            text-align: center;
            margin-bottom: 18px;
        }

        .brand .mark {
            width: 60px;
            height: 60px;
            margin: 0 auto 12px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--green), var(--green-2));
            color: #fff;
            font-weight: 800;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 22px rgba(15, 81, 50, .22);
        }

        .brand .mark img {
            max-width: 60%;
            max-height: 60%;
        }

        .brand h1 {
            font-size: 20px;
            font-weight: 800;
            color: #15211b;
            margin: 0;
            letter-spacing: -.2px;
        }

        .brand .sub {
            font-size: 11.5px;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: #7a867f;
            margin-top: 3px;
        }

        .login-box-body {
            border-radius: 16px !important;
            border: 1px solid #e4e9e5;
            box-shadow: 0 14px 40px rgba(15, 81, 50, .10);
            padding: 28px 26px 22px;
        }

        .login-box-msg {
            color: #15211b;
            font-weight: 700;
            padding-top: 0;
        }

        .login-box-body .form-control {
            height: 44px;
            border-radius: 9px;
        }

        .btn-primary {
            background: var(--green);
            border-color: var(--green);
            height: 44px;
            border-radius: 9px;
            font-weight: 600;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--green-2);
            border-color: var(--green-2);
        }

        .login-foot {
            text-align: center;
            font-size: 11.5px;
            color: #9aa39d;
            margin-top: 16px;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="brand">
            <div class="mark">
                <img src="{{ url('assets/images/logo.png') }}" alt=""
                    onerror="this.style.display='none';this.parentNode.innerHTML='N';">
            </div>
            <h1>{{ config('app.name') }}</h1>
            <div class="sub">Management Portal</div>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">Sign in to your account</p>

            <form action="{{ admin_url('auth/login') }}" method="post">
                <div class="form-group has-feedback {!! !$errors->has('username') ?: 'has-error' !!}">
                    @if ($errors->has('username'))
                        @foreach ($errors->get('username') as $message)
                            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $message }}</label><br>
                        @endforeach
                    @endif
                    <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}"
                        name="username" value="{{ old('username') }}" autofocus>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">
                    @if ($errors->has('password'))
                        @foreach ($errors->get('password') as $message)
                            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $message }}</label><br>
                        @endforeach
                    @endif
                    <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}"
                        name="password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-8">
                        @if (config('admin.auth.remember'))
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" name="remember" value="1"
                                        {{ !old('username') || old('remember') ? 'checked' : '' }}>
                                    {{ trans('admin.remember_me') }}
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="col-xs-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('admin.login') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="login-foot">Ndege Estate Limited</div>
    </div>

    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ admin_asset('vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });
    </script>
</body>

</html>
