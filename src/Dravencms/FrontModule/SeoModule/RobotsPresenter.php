<?php declare(strict_types = 1);

namespace Dravencms\FrontModule\SeoModule;

use Dravencms\BasePresenter;
use Dravencms\Seo\Robots\RobotsCollector;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RobotsPresenter extends BasePresenter
{
    /** @var RobotsCollector @inject */
    public $robotsCollector;

    public function renderDefault(): void
    {
        $groups = [];

        foreach ($this->robotsCollector->getDirectives() as $directive) {
            $path = $directive->getPath();
            if ($path === null) {
                $path = $this->link($directive->getDestination(), $directive->getParameters());
            }

            $groups[$directive->getUserAgent()][] = [
                'action' => $directive->getAction(),
                'path' => $path,
            ];
        }

        $this->template->groups = $groups;
    }

}
