<div
    class="relative w-full max-w-sm"
    x-data="{ open: false }"
    x-on:keydown.meta.k.window.prevent="$refs.searchInput.focus(); open = true"
    x-on:keydown.ctrl.k.window.prevent="$refs.searchInput.focus(); open = true"
    x-on:click.outside="open = false"
>
    <label for="help-support-search-input" class="sr-only">Search {{ $helpSupportName }}</label>

    <div class="relative">
        <svg
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>

        <input
            id="help-support-search-input"
            x-ref="searchInput"
            wire:model.live.debounce.300ms="query"
            type="search"
            placeholder="Search {{ $helpSupportName }}…"
            autocomplete="off"
            x-on:focus="open = true"
            x-on:input="open = true"
            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-16 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        />

        <kbd class="pointer-events-none absolute right-2 top-1/2 hidden -translate-y-1/2 rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[10px] font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500 sm:inline-block">
            ⌘K
        </kbd>
    </div>

    @if (strlen(trim($query)) >= (int) config('afterburner-playbook.search.min_query_length', 2))
        <div
            x-show="open"
            x-cloak
            class="absolute right-0 z-50 mt-2 w-[min(100vw-2rem,24rem)] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
        >
            @if ($results->isEmpty())
                <p class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    No results for &ldquo;{{ $query }}&rdquo;
                </p>
            @else
                <ul class="max-h-80 divide-y divide-gray-100 overflow-y-auto dark:divide-gray-800">
                    @foreach ($results as $result)
                        <li>
                            <a
                                href="{{ route('playbook.show', $result['route']) }}"
                                wire:navigate
                                class="block px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                                x-on:click="open = false"
                            >
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $result['title'] }}
                                </p>
                                <p class="mt-0.5 text-xs text-indigo-600 dark:text-indigo-400">
                                    {{ $result['section_label'] }}
                                    @if ($result['group'])
                                        <span class="text-gray-400 dark:text-gray-500">· {{ $result['group'] }}</span>
                                    @endif
                                </p>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $result['snippet'] }}
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>
