<div>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Find quick answers to common questions. Can&apos;t find what you need?
                Use the contact support button above to reach a system administrator.
            </p>
        </div>

        @if ($this->canManage())
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex shrink-0 items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add FAQ
            </button>
        @endif
    </div>

    <div class="mt-8 space-y-3" x-data="{ openId: null }">
        @forelse ($this->faqs as $faq)
            <div @class([
                'rounded-lg border transition',
                'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => $faq->is_published || $this->canManage(),
                'border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50' => ! $faq->is_published && $this->canManage(),
            ])>
                <div class="flex items-stretch">
                    <button
                        type="button"
                        x-on:click="openId = openId === {{ $faq->id }} ? null : {{ $faq->id }}"
                        class="flex min-w-0 flex-1 items-start gap-3 px-4 py-4 text-start transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        :aria-expanded="openId === {{ $faq->id }}"
                    >
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 transition"
                            :class="{ 'rotate-180': openId === {{ $faq->id }} }"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>

                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $faq->question }}</span>
                                @if ($this->canManage() && ! $faq->is_published)
                                    <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        Draft
                                    </span>
                                @endif
                            </span>
                        </span>
                    </button>

                    @if ($this->canManage())
                        <div class="flex shrink-0 items-center gap-1 border-s border-gray-200 px-2 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="moveFaq({{ $faq->id }}, 'up')"
                                @disabled($loop->first)
                                class="rounded p-1.5 text-gray-400 transition hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30 dark:hover:text-gray-300"
                                title="Move up"
                                aria-label="Move up"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75 12 8.25l7.5 7.5" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="moveFaq({{ $faq->id }}, 'down')"
                                @disabled($loop->last)
                                class="rounded p-1.5 text-gray-400 transition hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30 dark:hover:text-gray-300"
                                title="Move down"
                                aria-label="Move down"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25 12 15.75 4.5 8.25" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="openEditModal({{ $faq->id }})"
                                class="rounded p-1.5 text-gray-400 transition hover:text-indigo-600 dark:hover:text-indigo-400"
                                title="Edit FAQ"
                                aria-label="Edit FAQ"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="confirmDeletion({{ $faq->id }})"
                                class="rounded p-1.5 text-gray-400 transition hover:text-red-600 dark:hover:text-red-400"
                                title="Delete FAQ"
                                aria-label="Delete FAQ"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div
                    x-show="openId === {{ $faq->id }}"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="border-t border-gray-200 px-4 py-4 dark:border-gray-700"
                >
                    <div class="ps-8 text-sm leading-relaxed text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $faq->answer }}</div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center dark:border-gray-600">
                <svg class="mx-auto h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    @if ($this->canManage())
                        No FAQs yet. Add your first question to help users find answers quickly.
                    @else
                        No FAQs are available yet. Check back soon or contact support for help.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Create / edit modal --}}
    <div
        x-data="{ show: @entangle('showFormModal') }"
        x-on:keydown.escape.window="show = false"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="playbook-faq-form-modal-title"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 w-full transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800 sm:mx-auto sm:max-w-lg"
            x-trap.inert.noscroll="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="px-6 py-4">
                <h2 id="playbook-faq-form-modal-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $editingFaqId ? 'Edit FAQ' : 'Add FAQ' }}
                </h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="playbook-faq-question" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Question
                        </label>
                        <input
                            id="playbook-faq-question"
                            type="text"
                            wire:model="question"
                            autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                        />
                        @error('question')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="playbook-faq-answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Answer
                        </label>
                        <textarea
                            id="playbook-faq-answer"
                            wire:model="answer"
                            rows="6"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                        ></textarea>
                        @error('answer')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="playbook-faq-published"
                            type="checkbox"
                            wire:model="isPublished"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600"
                        />
                        <label for="playbook-faq-published" class="text-sm text-gray-700 dark:text-gray-300">
                            Published (visible to all users)
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-row justify-end bg-gray-100 px-6 py-4 text-end dark:bg-gray-800">
                <button
                    type="button"
                    wire:click="closeFormModal"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="saveFaq"
                    wire:loading.attr="disabled"
                    class="ms-3 inline-flex items-center rounded-md border border-transparent bg-landing-lake px-4 py-2 text-xs font-semibold uppercase tracking-widest text-landing-mist transition hover:bg-landing-lake-deep focus:bg-landing-lake-deep focus:outline-none focus:ring-2 focus:ring-landing-spruce focus:ring-offset-2 disabled:opacity-50 dark:focus:ring-offset-gray-800"
                >
                    {{ $editingFaqId ? 'Save changes' : 'Create FAQ' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div
        x-data="{ show: @entangle('confirmingDeletion') }"
        x-on:keydown.escape.window="show = false"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="playbook-faq-delete-modal-title"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="$wire.cancelDeletion()"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 w-full transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800 sm:mx-auto sm:max-w-md"
            x-trap.inert.noscroll="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="px-6 py-4">
                <h2 id="playbook-faq-delete-modal-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete FAQ
                </h2>
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete this FAQ? This action cannot be undone.
                </p>
            </div>

            <div class="flex flex-row justify-end bg-gray-100 px-6 py-4 text-end dark:bg-gray-800">
                <button
                    type="button"
                    wire:click="cancelDeletion"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="deleteFaq"
                    wire:loading.attr="disabled"
                    class="ms-3 inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 dark:focus:ring-offset-gray-800"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
