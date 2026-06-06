<x-app-layout :title="\Afterburner\Playbook\Support\PageHeader::make($helpSupportName, detail: 'FAQ')">
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <x-afterburner-playbook::page-header :section="$helpSupportName" detail="FAQ" />
            <div class="ms-auto flex shrink-0 items-center gap-2">
                <livewire:playbook-search />
                <livewire:playbook-contact-support />
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-[16rem_minmax(0,1fr)_14rem] lg:gap-10 py-8">
            <aside class="hidden lg:block">
                <div class="sticky top-6 max-h-[calc(100vh-3rem)] overflow-y-auto pb-8">
                    @include('afterburner-playbook::components.sidebar', [
                        'sidebarSections' => $sidebarSections,
                        'activeFaq' => true,
                        'section' => null,
                        'page' => null,
                    ])
                </div>
            </aside>

            <article class="min-w-0">
                @include('afterburner-playbook::components.mobile-sidebar', [
                    'sidebarSections' => $sidebarSections,
                    'activeFaq' => true,
                    'section' => null,
                    'page' => null,
                ])

                <header class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $helpSupportName }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Frequently asked questions</h1>
                </header>

                <livewire:playbook-faq-section />
            </article>

            <aside class="hidden xl:block">
                <div class="sticky top-6">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Need more help?</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Browse the guides in the sidebar or contact support using the button above.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
