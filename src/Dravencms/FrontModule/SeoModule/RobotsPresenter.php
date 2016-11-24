<?php

namespace Dravencms\FrontModule\SeoModule;

use App\Model\Structure\Repository\MenuRepository;
use Dravencms\GlobalPresenter;
use Dravencms\Model\Seo\Repository\RobotsRepository;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RobotsPresenter extends GlobalPresenter
{
    /** @var RobotsRepository @inject */
    public $robotsRepository;

    /** @var MenuRepository @inject */
    public $menuRepository;

    public function renderDefault()
    {
        $this->template->disabled = $this->menuRepository->getSitemap(false);

        $this->template->robots = $this->robotsRepository->getActive();
    }

}