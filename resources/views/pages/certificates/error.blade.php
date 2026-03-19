<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 420px;
            width: 90%;
        }

        .code {
            font-size: 64px;
            font-weight: bold;
            color: #dc3545;
        }

        .message {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }

        .sub {
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            background: #004D1A;
            color: #fff;
            font-size: 14px;
        }

        .btn:hover {
            background: #006622;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="code">{{ $code ?? 500 }}</div>

    <div class="message">
        {{ $message ?? 'Something went wrong.' }}
    </div>

    <div class="sub">
        {{ $sub ?? 'Please try again or contact the administrator.' }}
    </div>

    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard') }}" class="btn">
        Go Back
    </a>
</div>

</body>
</html>
