<?php

namespace Dravencms\FrontModule\SeoModule;

use Dravencms\BasePresenter;
use Dravencms\Model\Structure\Repository\MenuRepository;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class SitemapPresenter extends BasePresenter
{
    /** @var MenuRepository @inject */
    public $menuRepository;

    /** @var EntityManager @inject */
    public $entityManager;

    public function renderDefault(): void
    {
        $this->template->sitemap = $this->menuRepository->getSitemap();
    }

    public function renderStylesheet(): void
    {
        $this->template->itemURL = '{$itemURL}';
    }
}