<?php

namespace Afterburner\Playbook\Livewire;

use Afterburner\Playbook\PlaybookSearchService;
use Illuminate\Support\Collection;
use Livewire\Component;

class PlaybookSearch extends Component
{
    public string $query = '';

    /**
     * @return Collection<int, array{
     *     section_key: string,
     *     section_label: string,
     *     slug: string,
     *     title: string,
     *     group: string|null,
     *     snippet: string,
     *     score: int,
     *     route: array{section: string, page: string}
     * }>
     */
    public function results(): Collection
    {
        if (strlen(trim($this->query)) < (int) config('afterburner-playbook.search.min_query_length', 2)) {
            return collect();
        }

        return app(PlaybookSearchService::class)->search($this->query, auth()->user());
    }

    public function render()
    {
        return view('afterburner-playbook::livewire.playbook-search', [
            'results' => $this->results(),
        ]);
    }
}
