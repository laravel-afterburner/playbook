<?php

namespace Afterburner\Playbook\Console\Commands;

use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\Support\HelpSupportRoute;
use Illuminate\Console\Command;

class ValidatePlaybookCommand extends Command
{
    protected $signature = 'playbook:validate';

    protected $description = 'Validate playbook internal links and page discovery';

    public function handle(PlaybookRepository $repository): int
    {
        $failures = 0;

        foreach ($repository->visibleSections(null) as $section) {
            foreach ($repository->pagesForSection($section->key) as $page) {
                $contents = file_get_contents($page->filePath);

                $pattern = '/\]\(\/'.preg_quote(HelpSupportRoute::PREFIX, '/').'\/([a-z0-9-]+)\/([a-z0-9-]+)\)/';

                if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                    continue;
                }

                foreach ($matches as $match) {
                    $target = $repository->findPage($match[1], $match[2]);

                    if (! $target) {
                        $this->error("Broken link in {$page->sectionKey}/{$page->slug}: ".HelpSupportRoute::uri($match[1], $match[2]));
                        $failures++;
                    }
                }
            }
        }

        if ($failures > 0) {
            $this->error("Playbook validation failed with {$failures} broken link(s).");

            return Command::FAILURE;
        }

        $this->info('Playbook validation passed.');

        return Command::SUCCESS;
    }
}
