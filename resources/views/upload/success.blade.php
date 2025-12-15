<!DOCTYPE html>
<html>
<head>
    <title>Upload Complete!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #198754;
            color: #ffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 35px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            text-align: center;
        }
        .phone-box p {
            margin: 6px 0;
            font-size: 15.2px;
            color: #333;
        }
        .phone-box strong {
            color: #0d6efd;
        }
        .divider {
            border-top: 1px solid #e4e4e4;
            margin: 20px 0;
        }
        .label-text {
            color: #198754;
            font-weight: 600;
            font-size: 17px;
        }
    </style>
</head>
<body>

<div class="card">
    
    <h2 class="mb-2" style="color:#198754; font-weight:700;">Upload Complete</h2>

    <p class="label-text">File Uploaded Successfully!</p>

    <p class="mt-2 mb-1 text-danger font-weight-bold" style="font-weight:700">
        Please Contact Sharda Stationery & Xerox:
    </p>

    <div class="phone-box">
        <p>Printing Department: 
            <strong>+91 93720 04377</strong>
        </p>
        <p>Stationery Department: 
            <strong>+91 98690 04377</strong>
        </p>
        <p>Art & Craft Materials: 
            <strong>+91 98672 04377</strong>
        </p>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 15px;">
        <p style="font-size:15px;">
            <strong style="font-weight:700;">ID:</strong> {{ $session->custom_id }} &nbsp; | &nbsp;
            <strong style="font-weight:700;">Files:</strong> {{ $session->file_count }}
        </p>
        @if($session->message)
            <p><strong style="color:#198754; font-weight:700;">User Message:</strong> {{ $session->message }}</p>
        @endif
    </div>
    <small>
        <a href="{{ route('home') }}" class="btn btn-success btn-sm mt-3">Send More Files</a>
    </small>

</div>

</body>
</html>
