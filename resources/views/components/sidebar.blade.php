@php
    $activeFaq = $activeFaq ?? false;
    $activePage = $page ?? null;
    $activeSection = $section ?? null;
@endphp
<nav aria-label="{{ $helpSupportName }} sections" class="space-y-8">
    @if ($showFaqNav ?? false)
        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                Support
            </h2>

            <ul class="mt-3 space-y-1 border-l border-gray-200 dark:border-gray-700">
                <li>
                    <a
                        href="{{ route('playbook.faq') }}"
                        wire:navigate
                        @class([
                            'block border-l-2 py-1 pl-3 text-sm transition',
                            'border-landing-lake font-medium text-landing-lake dark:border-indigo-400 dark:text-indigo-400' => $activeFaq,
                            'border-transparent text-gray-700 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' => ! $activeFaq,
                        ])
                    >
                        FAQ
                    </a>
                </li>
            </ul>
        </div>
    @endif

    @foreach ($sidebarSections as $entry)
        @php
            $navSection = $entry['section'];
            $groups = $entry['groups'];
        @endphp

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
                {{ $navSection->label }}
            </h2>

            <div class="mt-3 space-y-4">
                @foreach ($groups as $group => $groupPages)
                    <div>
                        @if ($group !== 'General')
                            <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-500">{{ $group }}</p>
                        @endif

                        <ul class="space-y-1 border-l border-gray-200 dark:border-gray-700">
                            @foreach ($groupPages as $navPage)
                                @php
                                    $isActive = ! $activeFaq
                                        && $activeSection !== null
                                        && $activePage !== null
                                        && $activeSection->key === $navSection->key
                                        && $activePage->slug === $navPage->slug;
                                @endphp
                                <li>
                                    <a
                                        href="{{ route('playbook.show', $navPage->routeParameters()) }}"
                                        @class([
                                            'block border-l-2 py-1 pl-3 text-sm transition',
                                            'border-landing-lake font-medium text-landing-lake dark:border-indigo-400 dark:text-indigo-400' => $isActive,
                                            'border-transparent text-gray-700 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200' => ! $isActive,
                                        ])
                                    >
                                        {{ $navPage->displayTitle() }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
