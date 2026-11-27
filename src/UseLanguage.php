<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel;

use KboPublicSearch\KboPublicSearch;

final readonly class UseLanguage
{
    public function __invoke(KboPublicSearch $kbo, string $locale): void
    {
        match (strtolower($locale)) {
            'fr' => $kbo->useFrench(),
            default => $kbo->useDutch(),
        };
    }
}
