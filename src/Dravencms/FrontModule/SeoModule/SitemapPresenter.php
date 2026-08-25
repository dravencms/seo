<?php declare(strict_types = 1);

namespace Dravencms\FrontModule\SeoModule;

use Dravencms\BasePresenter;
use Dravencms\Seo\Sitemap\SitemapCollector;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class SitemapPresenter extends BasePresenter
{
    /** @var SitemapCollector @inject */
    public $sitemapCollector;

    public function renderDefault(): void
    {
        $sitemap = [];

        foreach ($this->sitemapCollector->getEntries() as $entry) {
            $alternates = [];
            foreach ($entry->getAlternates() as $alternate) {
                $alternates[] = [
                    'languageCode' => $alternate->getLanguageCode(),
                    'location' => $this->link('//' . $alternate->getDestination(), $alternate->getParameters()),
                ];
            }

            $sitemap[] = [
                'location' => $this->link('//' . $entry->getDestination(), $entry->getParameters()),
                'lastModified' => $entry->getLastModified(),
                'changeFrequency' => $entry->getChangeFrequency(),
                'priority' => $entry->getPriority(),
                'alternates' => $alternates,
            ];
        }

        $this->template->sitemap = $sitemap;
    }

    public function renderStylesheet(): void
    {
        $this->template->itemURL = '{$itemURL}';
    }
}
