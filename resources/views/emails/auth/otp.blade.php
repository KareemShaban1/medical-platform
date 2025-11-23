@php
    $appName = $appName ?? config('app.name');
    $appUrl = $appUrl ?? url('/');
    $ctaUrl = $ctaUrl ?? $appUrl;
    $ctaLabel = $ctaLabel ?? __('Open my dashboard');
    $expiryText = $expiryText ?? __('This code expires in :minutes minutes.', ['minutes' => 5]);
    $warningText = $warningText ?? __('Do not share this code with anyone.');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('Email Verification') }} - {{ $appName }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }
        .wrapper { width: 100%; padding: 24px 0; background: #f3f4f6; }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        }
        .header {
            background: linear-gradient(135deg, #079184, #10b981);
            padding: 20px 24px;
            color: #ffffff;
        }
        .header-title { margin: 0; font-size: 20px; font-weight: 700; }
        .header-subtitle { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .content { padding: 24px; }
        .greeting { font-size: 16px; margin: 0 0 8px; }
        .lead-text { font-size: 14px; margin: 0 0 16px; color: #4b5563; }
        .otp-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
        }
        .otp-code {
            font-size: 28px;
            letter-spacing: 6px;
            font-weight: 800;
            color: #0f766e;
            margin: 0 0 8px;
        }
        .muted { color: #6b7280; font-size: 13px; }
        .cta-wrapper { text-align: center; padding: 0 24px 24px; }
        .cta-button {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #079184, #10b981);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }
        .footer { padding: 0 24px 20px; font-size: 11px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1 class="header-title">{{ $title ?? __('Email Verification') }}</h1>
                <p class="header-subtitle">{{ $subtitle ?? __('Secure your account on :app', ['app' => $appName]) }}</p>
            </div>

            <div class="content">
                <p class="greeting">{{ __('Hello') }} {{ $name ?? '' }},</p>
                <p class="lead-text">{{ $intro ?? __('Use the one-time password below to verify your email.') }}</p>

                <div class="otp-card">
                    <p class="otp-code">{{ $otp ?? '' }}</p>
                    <p class="muted" style="margin: 0;">{{ $expiryText }}</p>
                </div>

                <p class="lead-text" style="margin-top: 0;">{{ $warningText }}</p>
            </div>

            {{-- @if(!empty($ctaUrl))
            <div class="cta-wrapper">
                <a href="{{ $ctaUrl }}" class="cta-button">{{ $ctaLabel }}</a>
            </div>
            @endif --}}

            <div class="footer">
                <p class="muted">{{ __('You received this email because you registered on :app.', ['app' => $appName]) }}</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
