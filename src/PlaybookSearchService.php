<?php

namespace Afterburner\Playbook;

use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Support\PlaybookPlaceholders;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PlaybookSearchService
{
    public const CACHE_KEY = 'afterburner.playbook.search.index';

    public function __construct(
        protected PlaybookRepository $repository,
    ) {}

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
    public function search(string $query, ?object $user, ?int $limit = null): Collection
    {
        $query = trim($query);

        if (strlen($query) < $this->minQueryLength()) {
            return collect();
        }

        $terms = $this->parseTerms($query);

        if ($terms->isEmpty()) {
            return collect();
        }

        $allowedKeys = $this->visiblePageKeys($user);

        return $this->index()
            ->filter(fn (array $document) => $allowedKeys->has($this->documentKey($document)))
            ->map(fn (array $document) => $this->scoreDocument($document, $terms))
            ->filter(fn (array $document) => $document['score'] > 0)
            ->sortByDesc('score')
            ->take($limit ?? $this->maxResults())
            ->values();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return Collection<int, array{
     *     section_key: string,
     *     section_label: string,
     *     slug: string,
     *     title: string,
     *     group: string|null,
     *     headings: string,
     *     body: string,
     *     route: array{section: string, page: string}
     * }>
     */
    protected function index(): Collection
    {
        $ttl = config('afterburner-playbook.search.cache_ttl');

        if ($ttl === null) {
            return Cache::rememberForever(self::CACHE_KEY, fn () => $this->buildIndex());
        }

        return Cache::remember(self::CACHE_KEY, (int) $ttl, fn () => $this->buildIndex());
    }

    /**
     * @return Collection<int, array{
     *     section_key: string,
     *     section_label: string,
     *     slug: string,
     *     title: string,
     *     group: string|null,
     *     headings: string,
     *     body: string,
     *     route: array{section: string, page: string}
     * }>
     */
    protected function buildIndex(): Collection
    {
        $sectionLabels = collect(Playbook::sections())->mapWithKeys(
            fn (PlaybookSection $section) => [$section->key => $section->label]
        );

        return $this->repository->allPages()->map(function (PlaybookPage $page) use ($sectionLabels) {
            $raw = file_get_contents($page->filePath);
            [, $body] = $this->repository->parseFrontMatter($raw);
            $body = PlaybookPlaceholders::replace($body);

            return [
                'section_key' => $page->sectionKey,
                'section_label' => (string) ($sectionLabels[$page->sectionKey] ?? $page->sectionKey),
                'slug' => $page->slug,
                'title' => PlaybookPlaceholders::replace($page->title),
                'group' => $page->group,
                'headings' => $this->extractHeadingsPlain($body),
                'body' => $this->plainTextFromMarkdown($body),
                'route' => [
                    'section' => $page->sectionKey,
                    'page' => $page->slug,
                ],
            ];
        })->values();
    }

    /**
     * @return Collection<string, true>
     */
    protected function visiblePageKeys(?object $user): Collection
    {
        $keys = collect();

        foreach ($this->repository->visibleSections($user) as $section) {
            foreach ($this->repository->pagesForSection($section->key, $user) as $page) {
                $keys->put($page->sectionKey.'.'.$page->slug, true);
            }
        }

        return $keys;
    }

    /**
     * @param  Collection<int, string>  $terms
     * @param  array{
     *     section_key: string,
     *     section_label: string,
     *     slug: string,
     *     title: string,
     *     group: string|null,
     *     headings: string,
     *     body: string,
     *     route: array{section: string, page: string}
     * }  $document
     * @return array{
     *     section_key: string,
     *     section_label: string,
     *     slug: string,
     *     title: string,
     *     group: string|null,
     *     snippet: string,
     *     score: int,
     *     route: array{section: string, page: string}
     * }
     */
    protected function scoreDocument(array $document, Collection $terms): array
    {
        $title = Str::lower($document['title']);
        $headings = Str::lower($document['headings']);
        $body = Str::lower($document['body']);
        $score = 0;

        foreach ($terms as $term) {
            if (str_contains($title, $term)) {
                $score += 10;
            }

            if (str_contains($headings, $term)) {
                $score += 5;
            }

            if (str_contains($body, $term)) {
                $score += 1;
            }
        }

        return [
            'section_key' => $document['section_key'],
            'section_label' => $document['section_label'],
            'slug' => $document['slug'],
            'title' => $document['title'],
            'group' => $document['group'],
            'snippet' => $this->snippet($document['body'], $terms),
            'score' => $score,
            'route' => $document['route'],
        ];
    }

    /**
     * @param  array{section_key: string, slug: string}  $document
     */
    protected function documentKey(array $document): string
    {
        return $document['section_key'].'.'.$document['slug'];
    }

    /**
     * @return Collection<int, string>
     */
    protected function parseTerms(string $query): Collection
    {
        return collect(preg_split('/\s+/u', Str::lower(trim($query)) ?: '', -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn (string $term) => $term !== '')
            ->values();
    }

    protected function extractHeadingsPlain(string $markdown): string
    {
        $headings = [];

        foreach (preg_split('/\r\n|\r|\n/', $markdown) as $line) {
            if (preg_match('/^#{1,6}\s+(.+)$/', trim($line), $matches)) {
                $headings[] = trim($matches[1]);
            }
        }

        return implode(' ', $headings);
    }

    protected function plainTextFromMarkdown(string $markdown): string
    {
        $text = preg_replace('/```.*?```/s', ' ', $markdown) ?? $markdown;
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text) ?? $text;
        $text = preg_replace('/^#+\s+/m', '', $text) ?? $text;
        $text = preg_replace('/[*_`~>|]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * @param  Collection<int, string>  $terms
     */
    protected function snippet(string $body, Collection $terms, int $radius = 80): string
    {
        $bodyLower = Str::lower($body);

        foreach ($terms as $term) {
            $position = mb_strpos($bodyLower, $term);

            if ($position === false) {
                continue;
            }

            $start = max(0, $position - $radius);
            $excerpt = mb_substr($body, $start, $radius * 2);
            $prefix = $start > 0 ? '…' : '';
            $suffix = ($start + mb_strlen($excerpt)) < mb_strlen($body) ? '…' : '';

            return trim($prefix.$excerpt.$suffix);
        }

        return Str::limit($body, 120);
    }

    protected function minQueryLength(): int
    {
        return (int) config('afterburner-playbook.search.min_query_length', 2);
    }

    protected function maxResults(): int
    {
        return (int) config('afterburner-playbook.search.max_results', 20);
    }
}
