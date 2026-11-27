<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Events\LocaleUpdated;
use KboPublicSearch\KboPublicSearch;

final readonly class UseLanguageWhenAppLocaleUpdated
{
    public function __construct(private Container $container, private UseLanguage $use) {}

    public function handle(LocaleUpdated $event): void
    {
        if (! $this->container->resolved(KboPublicSearch::class)) {
            return;
        }

        ($this->use)($this->container[KboPublicSearch::class], $event->locale);
    }
}
