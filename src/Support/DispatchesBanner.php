<?php

namespace Afterburner\Playbook\Support;

trait DispatchesBanner
{
    protected function banner(string $message): void
    {
        $this->dispatch('banner-message',
            style: 'success',
            message: $message,
        );
    }

    protected function dangerBanner(string $message): void
    {
        $this->dispatch('banner-message',
            style: 'danger',
            message: $message,
        );
    }
}
