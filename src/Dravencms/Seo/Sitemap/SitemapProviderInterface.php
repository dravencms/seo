<?php declare(strict_types = 1);

namespace Dravencms\Seo\Sitemap;

interface SitemapProviderInterface
{
    /** @return iterable<SitemapEntry> */
    public function getSitemapEntries(): iterable;
}
