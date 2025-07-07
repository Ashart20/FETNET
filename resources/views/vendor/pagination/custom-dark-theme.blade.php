@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-slate-500 bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary cursor-default rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                {{-- Menggunakan wire:click untuk navigasi, bukan href --}}
                <button type="button" wire:click="previousPage" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-text-main bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary rounded-md hover:text-gray-500 dark:hover:bg-dark-tertiary">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 dark:text-text-main bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary rounded-md hover:text-gray-500 dark:hover:bg-dark-tertiary">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 dark:text-slate-500 bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary cursor-default rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-text-secondary">
                    Menampilkan <span class="font-medium text-gray-900 dark:text-text-main">{{ $paginator->firstItem() }}</span> sampai <span class="font-medium text-gray-900 dark:text-text-main">{{ $paginator->lastItem() }}</span> dari <span class="font-medium text-gray-900 dark:text-text-main">{{ $paginator->total() }}</span> hasil
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true"><span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 dark:text-slate-500 bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary cursor-default rounded-l-md" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span></span>
                    @else
                        <button type="button" wire:click="previousPage" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 dark:text-text-secondary bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary rounded-l-md hover:text-gray-400 dark:hover:text-text-main dark:hover:bg-dark-tertiary">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true"><span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 dark:text-text-secondary bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary cursor-default">{{ $element }}</span></span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"><span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-indigo-600 dark:bg-brand-purple border border-indigo-500 dark:border-brand-purple cursor-default">{{ $page }}</span></span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 dark:text-text-secondary bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary hover:text-gray-500 dark:hover:text-text-main dark:hover:bg-dark-tertiary">{{ $page }}</button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 dark:text-text-secondary bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary rounded-r-md hover:text-gray-400 dark:hover:text-text-main dark:hover:bg-dark-tertiary">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    @else
                        <span aria-disabled="true"><span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 dark:text-slate-500 bg-white dark:bg-dark-secondary border border-gray-300 dark:border-dark-tertiary cursor-default rounded-r-md">
                           <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </span></span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
