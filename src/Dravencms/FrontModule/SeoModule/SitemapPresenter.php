<?php

namespace Dravencms\FrontModule\SeoModule;

use Dravencms\GlobalPresenter;
use App\Model\Structure\Repository\MenuRepository;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class SitemapPresenter extends GlobalPresenter
{
    /** @var MenuRepository @inject */
    public $menuRepository;

    /** @var EntityManager @inject */
    public $entityManager;

    public function renderDefault()
    {
        $this->template->translationRepository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
        $this->template->sitemap = $this->menuRepository->getSitemap();
    }

    public function renderStylesheet()
    {
        $this->template->itemURL = '{$itemURL}';
    }
}