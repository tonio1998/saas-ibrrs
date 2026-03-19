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

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h4, .header h5 {
            margin: 2px 0;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0 25px;
            text-decoration: underline;
        }

        .content {
            text-align: justify;
        }

        .indent {
            text-indent: 50px;
            margin-bottom: 10px;
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

        .qr svg {
            width: 100px;
            height: 100px;
        }

        .bold { font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h5>Republic of the Philippines</h5>
    <h5>Province of {{ strtoupper($cert->barangay->province ?? '________') }}</h5>
    <h5>Municipality of {{ strtoupper($cert->barangay->municipality ?? '________') }}</h5>
    <h4><b>Barangay {{ strtoupper($cert->barangay->name ?? '________') }}</b></h4>
</div>

<div class="title">
    CERTIFICATE OF INDIGENCY
</div>

<div class="content">

    <p class="indent">
        TO WHOM IT MAY CONCERN:
    </p>

    <p class="indent">
        This is to certify that
        <span class="bold">
            {{ strtoupper($cert->resident->full_name) }}
        </span>,
        of legal age, Filipino, and a bona fide resident of Barangay
        {{ strtoupper($cert->barangay->name ?? '________') }},
        Municipality of {{ strtoupper($cert->barangay->municipality ?? '________') }},
        Province of {{ strtoupper($cert->barangay->province ?? '________') }}.
    </p>

    <p class="indent">
        This further certifies that the above-named person belongs to an indigent family
        and has insufficient financial means to support his/her basic needs.
    </p>

    <p class="indent">
        This certification is issued upon the request of the above-named person for
        <span class="bold">{{ strtoupper($cert->purpose) }}</span>.
    </p>

    <p class="indent">
        Issued this {{ $cert->created_at->format('jS') }} day of {{ $cert->created_at->format('F Y') }}
        at Barangay {{ strtoupper($cert->barangay->name ?? '________') }}.
    </p>

</div>

<div class="signature">
    <p><b>___________________________</b></p>
    <p class="bold">
        {{ strtoupper($cert->barangay->chairman_name ?? 'PUNONG BARANGAY') }}
    </p>
    <p>Punong Barangay</p>
</div>

<div class="footer">

    <div class="qr">
        <div style="font-size:10px;">Scan to Verify</div>
        {!! $qr ?? '' !!}
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
