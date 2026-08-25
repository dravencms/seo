<?php declare(strict_types = 1);

namespace Dravencms\Seo\Robots;

interface RobotsProviderInterface
{
    /** @return iterable<RobotsDirective> */
    public function getRobotsDirectives(): iterable;
}
