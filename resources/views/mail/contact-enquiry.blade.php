<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Enquiry</title>
    <style>
        body { font-family: sans-serif; font-size: 15px; color: #374151; background: #f9fafb; margin: 0; padding: 32px 16px; }
        .card { background: #ffffff; border-radius: 8px; padding: 32px; max-width: 560px; margin: 0 auto; border: 1px solid #e5e7eb; }
        h2 { margin: 0 0 24px; font-size: 20px; color: #111827; }
        .row { margin-bottom: 16px; }
        .label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 4px; }
        .value { color: #111827; }
        .message { background: #f3f4f6; border-radius: 6px; padding: 16px; white-space: pre-wrap; line-height: 1.6; }
        .footer { margin-top: 24px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h2>New Enquiry &mdash; {{ config('seo.business_name') }}</h2>

        <div class="row">
            <div class="label">Name</div>
            <div class="value">{{ $senderName }}</div>
        </div>

        <div class="row">
            <div class="label">Email</div>
            <div class="value"><a href="mailto:{{ $senderEmail }}" style="color:#4f46e5">{{ $senderEmail }}</a></div>
        </div>

        @if($senderPhone)
        <div class="row">
            <div class="label">Phone</div>
            <div class="value"><a href="tel:{{ $senderPhone }}" style="color:#4f46e5">{{ $senderPhone }}</a></div>
        </div>
        @endif

        <div class="row">
            <div class="label">Message</div>
            <div class="message">{{ $enquiryMessage }}</div>
        </div>

        <div class="footer">
            Sent via the contact form on {{ config('app.url') }}
        </div>
    </div>
</body>
</html>
