<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTCS HIMS — Password Reset Request</title>
    <style>
        body {
            margin: 0; padding: 0;
            font-family: "Inter", ui-sans-serif, system-ui, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            background-color: #f4f6f8;
            color: #1e1e1e;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            max-width: 540px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #072918 0%, #0d4a2e 100%);
            padding: 28px 32px 24px;
            text-align: center;
        }
        .header-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: rgba(20, 199, 154, 0.15);
            border-radius: 8px;
            border: 1px solid rgba(20, 199, 154, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .header-name {
            font-family: "Space Grotesk", "Inter", sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: #f0fdf4;
            letter-spacing: -0.015em;
        }
        .header-sub {
            font-size: 0.62rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: rgba(240, 253, 244, 0.5);
            display: block;
            margin-top: 1px;
        }
        .body {
            padding: 36px 40px 32px;
        }
        .title {
            font-family: "Space Grotesk", "Inter", sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d3321;
            margin: 0 0 8px;
        }
        .subtitle {
            font-size: 0.88rem;
            color: #555f6a;
            margin: 0 0 24px;
            line-height: 1.5;
        }
        .btn-container {
            text-align: center;
            margin: 28px 0;
        }
        .reset-btn {
            display: inline-block;
            background-color: #14C79A;
            color: #072918 !important;
            font-family: "Space Grotesk", "Inter", sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(20, 199, 154, 0.3);
        }
        .expiry {
            font-size: 0.82rem;
            color: #777f88;
            text-align: center;
            margin-bottom: 24px;
        }
        .expiry strong {
            color: #d97706;
        }
        .security-note {
            background: #fff8f0;
            border-left: 3px solid #d97706;
            border-radius: 0 6px 6px 0;
            padding: 14px 16px;
            font-size: 0.82rem;
            color: #6b4c10;
            margin-bottom: 24px;
        }
        .security-note strong {
            display: block;
            margin-bottom: 4px;
            color: #5a3d0a;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e8ecef;
            padding: 20px 32px;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            line-height: 1.7;
        }
        .footer strong {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <span class="header-logo">
                <span class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 38 38" fill="none">
                        <polyline points="3,19 9,19 12,12 14,26 17,8 19,30 21,14 23,22 26,19 35,19"
                            fill="none" stroke="#14C79A" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="header-name">DTCS HIMS</span>
                    <span class="header-sub">Diagnostic, Treatment &amp; Clinical Services</span>
                </span>
            </span>
        </div>

        {{-- Body --}}
        <div class="body">
            <h1 class="title">Password Reset Request</h1>
            <p class="subtitle">
                Hello {{ $userName }},<br>
                We received a request to reset the password for your DTCS HIMS account. Click the button below to choose a new password.
            </p>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="reset-btn">Reset Password</a>
            </div>

            <p class="expiry">
                This link will expire in <strong>{{ $expiresMinutes }} {{ $expiresMinutes === 1 ? 'minute' : 'minutes' }}</strong>.
            </p>

            <div class="security-note">
                <strong>Did not request a password reset?</strong>
                If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged and your account stays secure.
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <strong>DTCS Hospital Information Management System</strong><br>
            This is an automated security message. Do not reply to this email.<br>
            Unauthorized access to DTCS HIMS is strictly prohibited.
        </div>

    </div>
</body>
</html>
