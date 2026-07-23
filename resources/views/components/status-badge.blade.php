@props(['status' => '', 'size' => 'sm'])

@php
    $status = strtoupper($status);

    $colors = [
        'ACTIVE'   => 'bg-green-100 text-green-800',
        'INACTIVE' => 'bg-red-100 text-red-800',
        'DRAFT'    => 'bg-gray-100 text-gray-800',
        'COMPLETED' => 'bg-blue-100 text-blue-800',
        'PAID'     => 'bg-green-100 text-green-800',
        'UNPAID'   => 'bg-red-100 text-red-800',
        'PARTIAL'  => 'bg-amber-100 text-amber-800',
        'PENDING'  => 'bg-yellow-100 text-yellow-800',
        'CANCELLED' => 'bg-gray-100 text-gray-800',
        'PROCESSING' => 'bg-indigo-100 text-indigo-800',
    ];

    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<span class="inline-flex items-center font-medium rounded-full {{ $colorClass }} {{ $sizeClass }}">
    {{ $status }}
</span>
