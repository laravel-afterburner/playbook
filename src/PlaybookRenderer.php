<?php

namespace Afterburner\Playbook;

use Afterburner\Playbook\Support\PlaybookPlaceholders;
use Illuminate\Support\Str;

class PlaybookRenderer
{
    public function __construct(
        protected PlaybookRepository $repository,
    ) {}

    /**
     * @return array{html: string, headings: list<array{id: string, text: string, level: int}>}
     */
    public function renderPage(PlaybookPage $page): array
    {
        $raw = file_get_contents($page->filePath);
        [, $body] = $this->repository->parseFrontMatter($raw);
        $body = $this->interpolatePlaceholders($body);
        $headings = $this->extractHeadings($body);
        $html = Str::markdown($body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = $this->injectHeadingAnchors($html, $headings);

        return [
            'html' => $html,
            'headings' => $headings,
        ];
    }

    protected function interpolatePlaceholders(string $content): string
    {
        return PlaybookPlaceholders::replace($content);
    }

    /**
     * @return list<array{id: string, text: string, level: int}>
     */
    protected function extractHeadings(string $markdown): array
    {
        $headings = [];

        foreach (preg_split('/\r\n|\r|\n/', $markdown) as $line) {
            if (! preg_match('/^(#{2,3})\s+(.+)$/', trim($line), $matches)) {
                continue;
            }

            $text = trim($matches[2]);
            $level = strlen($matches[1]);
            $id = Str::slug($text);

            $headings[] = [
                'id' => $id,
                'text' => $text,
                'level' => $level,
            ];
        }

        return $headings;
    }

    /**
     * @param  list<array{id: string, text: string, level: int}>  $headings
     */
    protected function injectHeadingAnchors(string $html, array $headings): string
    {
        foreach ($headings as $heading) {
            $pattern = '/<h'.$heading['level'].'>(.*?)<\/h'.$heading['level'].'>/';

            if (! preg_match($pattern, $html)) {
                continue;
            }

            $replacement = '<h'.$heading['level'].' id="'.$heading['id'].'"><a href="#'.$heading['id'].'" class="playbook-heading-anchor">'.$heading['text'].'</a></h'.$heading['level'].'>';
            $html = preg_replace($pattern, $replacement, $html, 1) ?? $html;
        }

        return $html;
    }
}
