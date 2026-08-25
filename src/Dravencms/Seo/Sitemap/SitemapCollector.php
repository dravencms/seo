<?php declare(strict_types = 1);

namespace Dravencms\Seo\Sitemap;

final class SitemapCollector
{
    /** @var list<SitemapProviderInterface> */
    private array $providers = [];

    public function addProvider(SitemapProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /** @return list<SitemapEntry> */
    public function getEntries(): array
    {
        $entries = [];

        foreach ($this->providers as $provider) {
            foreach ($provider->getSitemapEntries() as $entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }
}
