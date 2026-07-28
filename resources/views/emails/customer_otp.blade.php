<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your MYME verification code</title>
</head>
<body style="margin:0; padding:0; background:#f4f5f7; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px; width:100%; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.06);">
                    <tr>
                        <td style="background:#0f9d58; padding:24px; text-align:center;">
                            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="MYME" height="36" style="height:36px; display:inline-block;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px;">
                            <p style="margin:0 0 16px; color:#1a2238; font-size:15px; line-height:1.6;">Hi{{ isset($name) && $name ? ' ' . $name : '' }},</p>
                            <p style="margin:0 0 24px; color:#4a5568; font-size:15px; line-height:1.6;">{{ $intro }}</p>

                            <div style="text-align:center; margin:0 0 24px;">
                                <span style="display:inline-block; font-size:32px; font-weight:700; letter-spacing:8px; color:#0f9d58; background:#eef8f1; padding:14px 28px; border-radius:8px;">{{ $otp }}</span>
                            </div>

                            <p style="margin:0 0 8px; color:#4a5568; font-size:14px; line-height:1.6;">This code expires in <strong>{{ $expiryMinutes }} minutes</strong>.</p>
                            <p style="margin:0; color:#9ca3af; font-size:13px; line-height:1.6;">If you didn't request this, you can safely ignore this email — no action is needed.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px; background:#f9fafb; border-top:1px solid #eef0f2;">
                            <p style="margin:0; color:#9ca3af; font-size:12px; line-height:1.5;">MYME &middot; This is an automated message, please don't reply directly to this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
