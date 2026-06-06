@php
    $activeFaq = $activeFaq ?? false;
    $activePage = $page ?? null;
    $activeSection = $section ?? null;
    $currentUrl = url()->current();
@endphp

<div class="mb-6 lg:hidden">
    <label for="playbook-mobile-nav" class="block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">
        Browse {{ $helpSupportName }}
    </label>
    <select
        id="playbook-mobile-nav"
        class="mt-2 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
        onchange="if (this.value) { if (window.Livewire?.navigate) { window.Livewire.navigate(this.value); } else { window.location.href = this.value; } }"
    >
        @if ($showFaqNav ?? false)
            <option value="{{ route('playbook.faq') }}" @selected($activeFaq)>FAQ</option>
        @endif

        @foreach ($sidebarSections as $entry)
            @php
                $navSection = $entry['section'];
                $groups = $entry['groups'];
            @endphp
            @foreach ($groups as $group => $groupPages)
                @foreach ($groupPages as $navPage)
                    @php
                        $pageUrl = route('playbook.show', $navPage->routeParameters());
                        $label = $navSection->label.' — '.$navPage->displayTitle();
                        if ($group !== 'General') {
                            $label = $navSection->label.' / '.$group.' — '.$navPage->displayTitle();
                        }
                        $isSelected = ! $activeFaq
                            && $activeSection !== null
                            && $activePage !== null
                            && $activeSection->key === $navSection->key
                            && $activePage->slug === $navPage->slug;
                    @endphp
                    <option value="{{ $pageUrl }}" @selected($isSelected)>{{ $label }}</option>
                @endforeach
            @endforeach
        @endforeach
    </select>
</div>
