<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }

        .center { text-align: center; }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h4, .header h5, .header h6 {
            margin: 2px 0;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }

        .content {
            margin-top: 30px;
            text-align: justify;
        }

        .indent {
            text-indent: 50px;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            position: relative;
        }

        .qr {
            position: absolute;
            right: 0;
            top: 0;
            text-align: center;
        }

        .bold { font-weight: bold; }
        .qr {
            position: absolute;
            right: 0;
            top: 0;
            text-align: center;
        }

        .qr svg {
            width: 100px;
            height: 100px;
        }

    </style>
</head>
<body>

<div class="header">
    <h5>Republic of the Philippines</h5>
    <h5>Province of ________</h5>
    <h5>Municipality of ________</h5>
    <h4><b>Barangay ________</b></h4>
</div>

<div class="title">
    CERTIFICATE OF {{ strtoupper($cert->certificateType->name) }}
</div>

<div class="content">

    <p class="indent">
        TO WHOM IT MAY CONCERN:
    </p>

    <p class="indent">
        This is to certify that <span class="bold">{{ strtoupper($cert->resident->full_name) }}</span>,
        of legal age, Filipino, and a resident of Barangay ________, Municipality of ________, Province of ________,
        is known to be of good moral character and law-abiding citizen in the community.
    </p>

    <p class="indent">
        This certification is issued upon the request of the above-named person for
        <span class="bold">{{ strtoupper($cert->purpose) }}</span>.
    </p>

    <p class="indent">
        Issued this {{ now()->format('jS') }} day of {{ now()->format('F Y') }}
        at Barangay ________, Municipality of ________.
    </p>

</div>

<div class="signature">
    <p><b>___________________________</b></p>
    <p class="bold">Punong Barangay</p>
</div>

<div class="footer">

    <div class="qr">
        <div style="font-size:10px;">Scan to Verify</div>
    </div>

    <p>Control No: <b>{{ $cert->ControlNo }}</b></p>

    @if($cert->certificateRecord)
        <p>
            OR No: <b>{{ $cert->certificateRecord->or_number }}</b> |
            Amount Paid: <b>₱ {{ number_format($cert->certificateRecord->Fee,2) }}</b> |
            Date: <b>{{ \Carbon\Carbon::parse($cert->certificateRecord->payment_date)->format('M d, Y') }}</b>
        </p>
    @endif

</div>

</body>
</html>
