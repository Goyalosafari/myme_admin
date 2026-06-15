<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Admin</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <link rel="shortcut icon" href="{{asset('images/favicon.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<body>
    <div id="auth">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12 mx-auto">
                    <div class="card pt-4">
                        <div class="card-body">
                            <div class="text-center mb-5">
                                <img src="{{asset('images/favicon.svg')}}" height="48" class="mb-4">
                                <h3>Admin Sign In</h3>
                                <p>Enter your credentials to receive a login OTP.</p>
                            </div>

                            @if($errors->has('credentials'))
                                <div class="alert alert-danger" role="alert">
                                    <strong>{{ $errors->first('credentials') }}</strong>
                                </div>
                            @endif

                            @if(session('mail_error'))
                                <div class="alert alert-warning" role="alert">
                                    <strong>Mail Error:</strong> Could not send OTP email. Please fix the mail configuration.
                                </div>
                            @endif

                            <form action="{{ route('admin.login') }}" method="POST">
                                @csrf
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="email">Admin Email</label>
                                    <div class="position-relative">
                                        <input type="email"
                                               class="form-control {{ $errors->has('credentials') ? 'is-invalid' : '' }}"
                                               id="email" name="email"
                                               value="{{ old('email') }}"
                                               placeholder="admin@example.com" autocomplete="email">
                                        <div class="form-control-icon">
                                            <i data-feather="mail"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="password">Password</label>
                                    <div class="position-relative">
                                        <input type="password"
                                               class="form-control {{ $errors->has('credentials') ? 'is-invalid' : '' }}"
                                               id="password" name="password"
                                               placeholder="Enter your password"
                                               autocomplete="current-password">
                                        <div class="form-control-icon">
                                            <i data-feather="lock"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix">
                                    <button class="btn btn-primary float-end">Send OTP</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <script src="{{asset('js/main.js')}}"></script>
    <script>feather.replace();</script>
</body>

</html>
