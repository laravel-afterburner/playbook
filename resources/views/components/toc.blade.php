@if (count($headings) > 0)
    <div>
        <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400">
            On this page
        </h2>

        <ul class="mt-3 space-y-2 text-sm">
            @foreach ($headings as $heading)
                <li @class(['pl-0' => $heading['level'] === 2, 'pl-3' => $heading['level'] === 3])>
                    <a href="#{{ $heading['id'] }}" class="text-gray-700 hover:text-landing-lake dark:text-gray-400 dark:hover:text-indigo-400">
                        {{ $heading['text'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .playbook-heading-anchor {
        color: inherit;
        text-decoration: none;
    }

    .playbook-heading-anchor:hover {
        color: #2f4f57;
    }
</style>
