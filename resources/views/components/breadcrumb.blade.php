<nav aria-label="Playbook breadcrumb" class="text-sm text-gray-500 dark:text-gray-400">
    <ol class="flex flex-wrap items-center gap-2">
        <li>
            <a href="{{ route('playbook.index') }}" class="hover:text-red-600 dark:hover:text-red-400">Playbook</a>
        </li>
        <li aria-hidden="true">/</li>
        <li>
            <a href="{{ route('playbook.section', $section->key) }}" class="hover:text-red-600 dark:hover:text-red-400">{{ $section->label }}</a>
        </li>
        <li aria-hidden="true">/</li>
        <li class="font-medium text-gray-900 dark:text-gray-100">{{ $page->displayTitle() }}</li>
    </ol>
</nav>
