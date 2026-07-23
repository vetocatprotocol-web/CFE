@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $from = $paginator->firstItem() ?? 0;
        $to = $paginator->lastItem() ?? 0;
        $total = $paginator->total();
    @endphp

    <nav class="flex flex-col sm:flex-row items-center justify-between gap-4" role="navigation" aria-label="Pagination Navigation">
        <p class="text-sm text-gray-600">
            Showing <span class="font-medium text-gray-900">{{ $from }}</span>
            to <span class="font-medium text-gray-900">{{ $to }}</span>
            of <span class="font-medium text-gray-900">{{ number_format($total) }}</span> results
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </a>
            @endif

            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 py-1 text-sm text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @php
                            $delta = 2;
                            $start = max(1, $currentPage - $delta);
                            $end = min($lastPage, $currentPage + $delta);
                            $shouldShowFirst = $start > 1;
                            $shouldShowLast = $end < $lastPage;
                        @endphp

                        @if ($shouldShowFirst)
                            <a href="{{ $element[1] }}"
                               class="w-9 h-9 inline-flex items-center justify-center text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-150">
                                1
                            </a>
                            @if ($start > 2)
                                <span class="px-1 text-gray-400">...</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if (array_key_exists($i, $element))
                                @if ($i == $currentPage)
                                    <span class="w-9 h-9 inline-flex items-center justify-center text-sm font-semibold text-white bg-blue-600 border border-blue-600 rounded-lg shadow-sm">
                                        {{ $i }}
                                    </span>
                                @else
                                    <a href="{{ $element[$i] }}"
                                       class="w-9 h-9 inline-flex items-center justify-center text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-150">
                                        {{ $i }}
                                    </a>
                                @endif
                            @endif
                        @endfor

                        @if ($shouldShowLast)
                            @if ($end < $lastPage - 1)
                                <span class="px-1 text-gray-400">...</span>
                            @endif
                            @if (array_key_exists($lastPage, $element))
                                <a href="{{ $element[$lastPage] }}"
                                   class="w-9 h-9 inline-flex items-center justify-center text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-150">
                                    {{ $lastPage }}
                                </a>
                            @endif
                        @endif
                    @endif
                @endforeach
            </div>

            <span class="sm:hidden px-3 py-2 text-sm text-gray-700 font-medium">
                Page {{ $currentPage }} of {{ $lastPage }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-150">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
