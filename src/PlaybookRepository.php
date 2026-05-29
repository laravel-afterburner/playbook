<?php

namespace Afterburner\Playbook;

use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Support\PlaybookAccess;
use App\Support\Features;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class PlaybookRepository
{
    /** @var array<string, list<PlaybookPage>>|null */
    protected ?array $pagesBySection = null;

    /**
     * @return Collection<int, PlaybookSection>
     */
    public function visibleSections(?object $user): Collection
    {
        return collect(Playbook::visibleSections($user));
    }

    /**
     * @return Collection<int, PlaybookPage>
     */
    public function pagesForSection(string $sectionKey, ?object $user = null): Collection
    {
        return collect($this->pagesBySection()[$sectionKey] ?? [])
            ->filter(fn (PlaybookPage $page) => $this->pageIsVisible($page, $user))
            ->sortBy([
                fn (PlaybookPage $page) => (int) ($page->meta['group_order'] ?? 100),
                ['group', 'asc'],
                ['order', 'asc'],
                ['title', 'asc'],
            ])
            ->values();
    }

    public function findPage(string $sectionKey, string $pageSlug, ?object $user = null): ?PlaybookPage
    {
        return $this->pagesForSection($sectionKey, $user)
            ->first(fn (PlaybookPage $page) => $page->slug === $pageSlug);
    }

    /**
     * @return Collection<int, PlaybookPage>
     */
    public function allPages(): Collection
    {
        return collect($this->pagesBySection())
            ->flatMap(fn (array $pages) => $pages)
            ->values();
    }

    public function isPageVisible(PlaybookPage $page, ?object $user): bool
    {
        return $this->pageIsVisible($page, $user);
    }

    public function firstPage(?object $user = null): ?PlaybookPage
    {
        foreach ($this->visibleSections($user) as $section) {
            $page = $this->pagesForSection($section->key, $user)->first();

            if ($page) {
                return $page;
            }
        }

        return null;
    }

    public function defaultPage(?object $user = null): ?PlaybookPage
    {
        $sectionKey = config('afterburner-playbook.default_section', 'platform');
        $pageSlug = config('afterburner-playbook.default_page', 'welcome');

        $page = $this->findPage($sectionKey, $pageSlug, $user);

        if ($page) {
            return $page;
        }

        return $this->firstPage($user);
    }

    /**
     * @return array<string, list<PlaybookPage>>
     */
    protected function pagesBySection(): array
    {
        if ($this->pagesBySection !== null) {
            return $this->pagesBySection;
        }

        $this->pagesBySection = [];

        foreach (Playbook::sections() as $section) {
            $this->pagesBySection[$section->key] = $this->discoverPages($section);
        }

        return $this->pagesBySection;
    }

    /**
     * @return list<PlaybookPage>
     */
    protected function discoverPages(PlaybookSection $section): array
    {
        if (! is_dir($section->path)) {
            return [];
        }

        $pages = [];

        $finder = (new Finder)
            ->files()
            ->in($section->path)
            ->name('*.md')
            ->notName('_*.md')
            ->sortByName();

        foreach ($finder as $file) {
            $relativePath = Str::after($file->getPathname(), rtrim($section->path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
            $groupDirectory = dirname($relativePath);

            if ($groupDirectory === '.') {
                $groupDirectory = null;
            }

            $raw = file_get_contents($file->getPathname());
            [$meta] = $this->parseFrontMatter($raw);

            $slug = (string) ($meta['slug'] ?? pathinfo($file->getFilename(), PATHINFO_FILENAME));
            $title = (string) ($meta['title'] ?? Str::headline($slug));
            $order = (int) ($meta['order'] ?? $this->orderFromFilename($file->getFilename()));
            $group = isset($meta['group'])
                ? (string) $meta['group']
                : ($groupDirectory ? Str::headline(str_replace('/', ' ', $groupDirectory)) : null);

            $requiresSystemAdmin = $this->pageRequiresSystemAdmin($meta, $relativePath);
            $meta['group_order'] = $this->groupOrder($groupDirectory, $meta);

            $pages[] = new PlaybookPage(
                sectionKey: $section->key,
                slug: $slug,
                title: $title,
                filePath: $file->getPathname(),
                order: $order,
                group: $group,
                feature: isset($meta['feature']) ? (string) $meta['feature'] : null,
                systemAdmin: $requiresSystemAdmin,
                meta: $meta,
            );
        }

        return $pages;
    }

    /**
     * @return array{0: array<string, mixed>, 1: string}
     */
    public function parseFrontMatter(string $contents): array
    {
        if (! preg_match('/^---\r?\n(.*?)\r?\n---\r?\n/s', $contents, $matches, PREG_OFFSET_CAPTURE)) {
            return [[], $contents];
        }

        try {
            $meta = Yaml::parse($matches[1][0]) ?? [];
        } catch (ParseException) {
            $meta = [];
        }

        if (! is_array($meta)) {
            $meta = [];
        }

        $body = substr($contents, $matches[0][1] + strlen($matches[0][0]));

        return [$meta, $body];
    }

    protected function orderFromFilename(string $filename): int
    {
        if (preg_match('/^(\d+)[-_]/', $filename, $matches)) {
            return (int) $matches[1];
        }

        return 100;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function groupOrder(?string $groupDirectory, array $meta): int
    {
        if (isset($meta['group_order'])) {
            return (int) $meta['group_order'];
        }

        if ($groupDirectory === null) {
            return 100;
        }

        $basename = basename(str_replace('\\', '/', $groupDirectory));

        if (strtolower($basename) === 'getting-started') {
            return 0;
        }

        return $this->orderFromFilename($basename);
    }

    protected function pageIsVisible(PlaybookPage $page, ?object $user): bool
    {
        if ($page->systemAdmin && ! PlaybookAccess::isSystemAdmin($user)) {
            return false;
        }

        if ($page->feature && class_exists(Features::class)) {
            return Features::enabled($page->feature);
        }

        return true;
    }

    protected function pageRequiresSystemAdmin(array $meta, string $relativePath): bool
    {
        if (array_key_exists('system_admin', $meta)) {
            return (bool) $meta['system_admin'];
        }

        if (array_key_exists('system-admin', $meta)) {
            return (bool) $meta['system-admin'];
        }

        return $this->pathRequiresSystemAdmin($relativePath);
    }

    protected function pathRequiresSystemAdmin(string $relativePath): bool
    {
        return str_contains(strtolower(str_replace('\\', '/', $relativePath)), 'system-admin');
    }

    public function flush(): void
    {
        $this->pagesBySection = null;

        if (app()->bound(PlaybookSearchService::class)) {
            app(PlaybookSearchService::class)->flush();
        }
    }
}
