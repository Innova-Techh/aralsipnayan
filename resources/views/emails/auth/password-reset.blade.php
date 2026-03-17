@php
    $appName = config('app.name', 'AralSipnayan');
    $brandBlue = '#1D4ED8'; // matches primary-blue vibe
    $brandYellow = '#FACC15'; // matches primary-yellow vibe
    $bgDark = '#0B1220';
    $card = '#111C33';
    $text = '#E5E7EB';
    $muted = '#94A3B8';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Reset Password</title>
</head>
<body style="margin:0;padding:0;background:{{ $bgDark }};font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:{{ $bgDark }};padding:24px 0;">
        <tr>
            <td align="center" style="padding:0 16px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;width:100%;">
                    <tr>
                        <td style="padding:8px 0 16px 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="left" style="color:#ffffff;font-size:20px;font-weight:700;">
                                        <span style="color:#ffffff;">Aral</span><span style="color:{{ $brandYellow }};">Sipnayan</span>
                                    </td>
                                    <td align="right" style="color:{{ $muted }};font-size:12px;">
                                        Password Reset
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:{{ $card }};border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:28px;">
                            <h1 style="margin:0 0 10px 0;color:#ffffff;font-size:22px;line-height:1.3;">
                                Reset your password
                            </h1>
                            <p style="margin:0 0 18px 0;color:{{ $text }};font-size:14px;line-height:1.6;">
                                We received a request to reset the password for your {{ $appName }} account{{ $userEmail ? ' (' . e($userEmail) . ')' : '' }}.
                                Click the button below to set a new password.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:18px 0 18px 0;">
                                <tr>
                                    <td align="center" bgcolor="{{ $brandBlue }}" style="border-radius:12px;">
                                        <a href="{{ $resetUrl }}" target="_blank" rel="noopener"
                                            style="display:inline-block;padding:12px 18px;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 10px 0;color:{{ $muted }};font-size:12px;line-height:1.6;">
                                This link will expire in {{ (int) $expireMinutes }} minutes.
                            </p>
                            <p style="margin:0;color:{{ $muted }};font-size:12px;line-height:1.6;">
                                If you didn’t request a password reset, you can safely ignore this email.
                            </p>

                            <div style="height:18px;line-height:18px;">&nbsp;</div>

                            <p style="margin:0;color:{{ $muted }};font-size:12px;line-height:1.6;">
                                Trouble clicking the button? Copy and paste this link into your browser:
                            </p>
                            <p style="margin:6px 0 0 0;word-break:break-all;color:#ffffff;font-size:12px;line-height:1.6;">
                                <a href="{{ $resetUrl }}" style="color:#ffffff;text-decoration:underline;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 0 0 0;color:{{ $muted }};font-size:12px;line-height:1.6;text-align:center;">
                            © {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

