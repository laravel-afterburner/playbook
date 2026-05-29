<?php

namespace Afterburner\Playbook\Http\Controllers;

use Afterburner\Playbook\PlaybookRenderer;
use Afterburner\Playbook\PlaybookRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PlaybookController extends Controller
{
    public function __construct(
        protected PlaybookRepository $repository,
        protected PlaybookRenderer $renderer,
    ) {}

    public function index(): RedirectResponse|View
    {
        $page = $this->repository->defaultPage(auth()->user());

        if (! $page) {
            abort(404, 'No playbook pages are available.');
        }

        return redirect()->route('playbook.show', $page->routeParameters());
    }

    public function section(string $section): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->repository->visibleSections($user)->contains(fn ($item) => $item->key === $section)) {
            abort(404);
        }

        $page = $this->repository->pagesForSection($section, $user)->first();

        if (! $page) {
            abort(404);
        }

        return redirect()->route('playbook.show', $page->routeParameters());
    }

    public function show(string $section, string $page): View
    {
        $user = auth()->user();

        if (! $this->repository->visibleSections($user)->contains(fn ($item) => $item->key === $section)) {
            abort(404);
        }

        $playbookPage = $this->repository->findPage($section, $page, $user);

        if (! $playbookPage) {
            abort(404);
        }

        $rendered = $this->renderer->renderPage($playbookPage);
        $visibleSections = $this->repository->visibleSections($user);
        $currentSection = $visibleSections->firstWhere('key', $section);

        $sidebarSections = $visibleSections->map(function ($navSection) use ($user) {
            $groups = $this->repository->pagesForSection($navSection->key, $user)
                ->groupBy(fn ($item) => $item->group ?? 'General')
                ->filter(fn ($groupPages) => $groupPages->isNotEmpty());

            return [
                'section' => $navSection,
                'groups' => $groups,
            ];
        })->filter(fn (array $item) => $item['groups']->isNotEmpty())->values();

        return view('afterburner-playbook::show', [
            'page' => $playbookPage,
            'section' => $currentSection,
            'sidebarSections' => $sidebarSections,
            'content' => $rendered['html'],
            'headings' => $rendered['headings'],
        ]);
    }
}
