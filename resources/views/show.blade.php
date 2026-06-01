<x-app-layout :title="\Afterburner\Playbook\Support\PageHeader::make($helpSupportName, detail: $page->displayTitle())">
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <x-afterburner-playbook::page-header :section="$helpSupportName" :detail="$page->displayTitle()" />
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
                        'section' => $section,
                        'page' => $page,
                        'activeFaq' => false,
                    ])
                </div>
            </aside>

            <article class="min-w-0">
                <header class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $section->label }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $page->displayTitle() }}</h1>
                </header>

                <div class="playbook-prose prose prose-slate dark:prose-invert max-w-none prose-headings:scroll-mt-24 prose-a:text-indigo-600 hover:prose-a:text-indigo-500 dark:prose-a:text-indigo-400 dark:hover:prose-a:text-indigo-300 prose-code:before:content-none prose-code:after:content-none prose-code:text-indigo-700 dark:prose-code:text-indigo-300 prose-code:bg-gray-100 dark:prose-code:bg-gray-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded">
                    {!! $content !!}
                </div>
            </article>

            <aside class="hidden xl:block">
                <div class="sticky top-6">
                    @include('afterburner-playbook::components.toc', ['headings' => $headings])
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
