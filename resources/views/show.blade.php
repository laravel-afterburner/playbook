<x-app-layout :title="$page->displayTitle().' - Playbook'">
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            @include('afterburner-playbook::components.breadcrumb', [
                'section' => $section,
                'page' => $page,
            ])
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-[16rem_minmax(0,1fr)_14rem] lg:gap-10 py-8">
            <aside class="hidden lg:block">
                <div class="sticky top-6 max-h-[calc(100vh-3rem)] overflow-y-auto pb-8">
                    @include('afterburner-playbook::components.sidebar', [
                        'sidebarSections' => $sidebarSections,
                        'section' => $section,
                        'page' => $page,
                    ])
                </div>
            </aside>

            <article class="min-w-0">
                <header class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $section->label }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $page->displayTitle() }}</h1>
                </header>

                <div class="playbook-prose prose prose-slate dark:prose-invert max-w-none prose-headings:scroll-mt-24 prose-a:text-red-600 hover:prose-a:text-red-500 dark:prose-a:text-red-400 dark:hover:prose-a:text-red-300 prose-code:before:content-none prose-code:after:content-none prose-code:text-red-700 dark:prose-code:text-red-300 prose-code:bg-gray-100 dark:prose-code:bg-gray-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded">
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
