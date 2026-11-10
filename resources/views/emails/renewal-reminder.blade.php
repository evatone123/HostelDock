<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Renewal Window Open</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f4f6f9; margin: 0; padding: 24px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; }
        .header { background: #7c3aed; color: #ffffff; padding: 24px 28px; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 28px; color: #334155; line-height: 1.6; }
        .body p { margin: 0 0 14px; }
        .details { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 18px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 6px 0; font-size: 14px; }
        .details td:first-child { color: #64748b; }
        .details td:last-child { text-align: right; font-weight: 600; color: #0f172a; }
        .alert { background: #fdf4ff; border: 1px solid #e9d5ff; color: #5b21b6; border-radius: 8px; padding: 14px 18px; font-size: 14px; margin-bottom: 18px; }
        .footer { padding: 16px 28px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 12px; text-align: center; }
        .btn { display: inline-block; background: #7c3aed; color: #ffffff; text-decoration: none; padding: 12px 22px; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'HostelDock') }} &mdash; Renewal Window Open</h1>
        </div>
        <div class="body">
            <p>Hello {{ $booking->student_name }},</p>
            <p>Your room occupancy is coming to an end soon. Renew it to keep your spot for another {{ \App\Models\Booking::OCCUPANCY_DAYS }} days.</p>

            <div class="details">
                <table>
                    <tr><td>Reference</td><td>{{ $booking->booking_ref }}</td></tr>
                    <tr><td>Hostel</td><td>{{ $booking->hostel->displayName() }}</td></tr>
                    <tr><td>Room</td><td>{{ $booking->room_type }}</td></tr>
                    <tr><td>Occupancy ends</td><td>{{ $booking->expires_at ? $booking->expires_at->format('j M Y') : 'N/A' }}</td></tr>
                    <tr><td>Renewal fee</td><td>GHS {{ number_format($booking->renewalPrice(), 2) }}</td></tr>
                </table>
            </div>

            <div class="alert">Your renewal window is open now. Once you lock your room, you can pay any time before the expiry date to extend your stay.</div>

            <p style="text-align:center; margin: 22px 0;">
                <a class="btn" href="{{ route('my-bookings') }}">Lock &amp; Renew My Room</a>
            </p>

            <p style="font-size: 13px; color: #64748b;">If you do not renew before your expiry date, the room becomes available to other students.</p>
        </div>
        <div class="footer">&copy; {{ date('Y') }} {{ config('app.name', 'HostelDock') }}</div>
    </div>
</body>
</html>