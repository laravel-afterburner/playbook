<?php

namespace Afterburner\Playbook;

use Afterburner\Playbook\Support\PlaybookPlaceholders;

class PlaybookPage
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public readonly string $sectionKey,
        public readonly string $slug,
        public readonly string $title,
        public readonly string $filePath,
        public readonly int $order = 100,
        public readonly ?string $group = null,
        public readonly ?string $feature = null,
        public readonly bool $systemAdmin = false,
        public readonly array $meta = [],
    ) {}

    public function routeName(): string
    {
        return 'playbook.show';
    }

    /**
     * @return array<string, string>
     */
    public function routeParameters(): array
    {
        return [
            'section' => $this->sectionKey,
            'page' => $this->slug,
        ];
    }

    public function displayTitle(): string
    {
        return PlaybookPlaceholders::replace($this->title);
    }
}
