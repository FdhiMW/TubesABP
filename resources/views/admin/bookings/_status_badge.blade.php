@php
    // Default
    $bg = '#e5e7eb';
    $fg = '#374151';
    $label = ucfirst($status);

    if ($status == 'pending') {
        $bg = '#fef3c7';
        $fg = '#92400e';
        $label = 'Pending';

    } elseif ($status == 'cancelled') {
        $bg = '#fee2e2';
        $fg = '#991b1b';
        $label = 'Cancelled';

    } elseif ($status == 'completed') {
        $bg = '#e0e7ff';
        $fg = '#3730a3';
        $label = 'Selesai';

    } elseif ($status == 'confirmed') {

        if ($payment_status == 'paid') {
            // ✅ SUDAH BAYAR
            $bg = '#d1fae5';
            $fg = '#065f46';
            $label = 'Lunas';

        } else {
            // ⏳ BELUM BAYAR
            $bg = '#dbeafe';
            $fg = '#1e40af';
            $label = 'Menunggu Bayar';
        }
    }
@endphp

<span style="background:{{ $bg }}; color:{{ $fg }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold; white-space:nowrap;">
    {{ $label }}
</span>