<!DOCTYPE html>
<html lang="en">
<body style="margin:0; padding:24px; background:#f3f5f9; font-family: Arial, Helvetica, sans-serif; color:#1d1d1d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:8px; border-top:4px solid #0b5cc4;">
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 12px; font-size:16px;">Password reset request</p>
                <p style="margin:0 0 16px; font-size:14px; color:#444;">Use this OTP to set a new password for {{ config('app.name') }}:</p>
                <p style="margin:0 0 16px; font-size:32px; font-weight:bold; letter-spacing:8px; color:#0b5cc4; text-align:center;">{{ $otp }}</p>
                <p style="margin:0 0 8px; font-size:13px; color:#444;">This OTP is valid for {{ $minutes }} minutes.</p>
                <p style="margin:0; font-size:13px; color:#888;">If you did not ask for this, ignore this email. Your password will not change.</p>
            </td>
        </tr>
    </table>
</body>
</html>
