<?php declare(strict_types = 1);

namespace Dravencms\Seo\Sitemap;

final class SitemapEntry
{
    /**
     * @param array<string, mixed> $parameters
     * @param list<SitemapAlternate> $alternates
     */
    public function __construct(
        private string $destination,
        private array $parameters = [],
        private ?\DateTimeInterface $lastModified = null,
        private ?string $changeFrequency = null,
        private ?float $priority = null,
        private array $alternates = []
    ) {
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

    public function getLastModified(): ?\DateTimeInterface
    {
        return $this->lastModified;
    }

    public function getChangeFrequency(): ?string
    {
        return $this->changeFrequency;
    }

    public function getPriority(): ?float
    {
        return $this->priority;
    }

    /** @return list<SitemapAlternate> */
    public function getAlternates(): array
    {
        return $this->alternates;
    }
}
