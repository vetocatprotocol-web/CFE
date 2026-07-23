@props(['status' => '', 'size' => 'sm'])

@php
    $status = strtoupper($status);

    $colors = [
        'ACTIVE'     => 'bg-green-100 text-green-800 ring-green-500/20',
        'INACTIVE'   => 'bg-red-100 text-red-800 ring-red-500/20',
        'DRAFT'      => 'bg-gray-100 text-gray-700 ring-gray-500/20',
        'COMPLETED'  => 'bg-emerald-100 text-emerald-800 ring-emerald-500/20',
        'PAID'       => 'bg-green-100 text-green-800 ring-green-500/20',
        'UNPAID'     => 'bg-red-100 text-red-800 ring-red-500/20',
        'PARTIAL'    => 'bg-amber-100 text-amber-800 ring-amber-500/20',
        'PENDING'    => 'bg-yellow-100 text-yellow-800 ring-yellow-500/20',
        'CANCELLED'  => 'bg-gray-100 text-gray-600 ring-gray-500/20',
        'PROCESSING' => 'bg-indigo-100 text-indigo-800 ring-indigo-500/20',
        'OPEN'       => 'bg-blue-100 text-blue-800 ring-blue-500/20',
        'SETTLED'    => 'bg-teal-100 text-teal-800 ring-teal-500/20',
        'ARCHIVED'   => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        'FAILED'     => 'bg-red-100 text-red-800 ring-red-500/20',
    ];

    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-700 ring-gray-500/20';

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1 text-sm',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<span class="inline-flex items-center font-semibold rounded-full ring-1 ring-inset {{ $colorClass }} {{ $sizeClass }}">
    {{ $status }}
</span>
