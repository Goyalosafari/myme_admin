<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Admin</title>
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
                                <h3>Verify OTP</h3>
                                <p>A 6-digit OTP has been sent to your admin email.</p>
                            </div>

                            @if(config('app.debug') && session('admin_otp'))
                                <div class="alert alert-warning text-center" role="alert">
                                    <strong>Dev Mode &mdash; SMTP not configured</strong><br>
                                    Your OTP is:
                                    <div style="font-size:2rem;font-weight:bold;letter-spacing:8px;margin-top:6px;">
                                        {{ session('admin_otp') }}
                                    </div>
                                </div>
                            @endif

                            @if(session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            <form action="{{ route('admin.otp.verify') }}" method="POST">
                                @csrf
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="otp">Enter OTP</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control text-center fs-4 fw-bold ls-4"
                                               id="otp" name="otp" maxlength="6"
                                               placeholder="------" autocomplete="off"
                                               value="{{ old('otp') }}">
                                        @error('otp')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                        <div class="form-control-icon">
                                            <i data-feather="shield"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix">
                                    <button class="btn btn-primary float-end">Verify OTP</button>
                                    <a href="{{ route('admin.login') }}" class="btn btn-light float-start">Back</a>
                                </div>
                            </form>

                            <p class="text-center text-muted mt-4 small">OTP expires in 5 minutes.</p>
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
