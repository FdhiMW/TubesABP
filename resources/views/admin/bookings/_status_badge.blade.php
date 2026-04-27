@php
    $colorMap = [
        'pending'          => ['#fef3c7', '#92400e'],
        'awaiting_payment' => ['#dbeafe', '#1e40af'],
        'paid'             => ['#d1fae5', '#065f46'],
        'confirmed'        => ['#d1fae5', '#065f46'],
        'cancelled'        => ['#fee2e2', '#991b1b'],
        'completed'        => ['#e0e7ff', '#3730a3'],
    ];
    $labels = [
        'pending'          => 'Pending',
        'awaiting_payment' => 'Menunggu Bayar',
        'paid'             => 'Sudah Bayar',
        'confirmed'        => 'Confirmed',
        'cancelled'        => 'Cancelled',
        'completed'        => 'Selesai',
    ];
    [$bg, $fg] = $colorMap[$status] ?? ['#e5e7eb', '#374151'];
@endphp
<span style="background:{{ $bg }}; color:{{ $fg }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold; white-space:nowrap;">
    {{ $labels[$status] ?? $status }}
</span>