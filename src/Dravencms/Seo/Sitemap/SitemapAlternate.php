<?php declare(strict_types = 1);

namespace Dravencms\Seo\Sitemap;

final class SitemapAlternate
{
    /** @param array<string, mixed> $parameters */
    public function __construct(
        private string $languageCode,
        private string $destination,
        private array $parameters = []
    ) {
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    /** @return array<string, mixed> */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
