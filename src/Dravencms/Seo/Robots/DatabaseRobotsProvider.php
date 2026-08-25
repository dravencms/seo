<?php declare(strict_types = 1);

namespace Dravencms\Seo\Robots;

use Dravencms\Model\Seo\Repository\RobotsRepository;

final class DatabaseRobotsProvider implements RobotsProviderInterface
{
    public function __construct(private RobotsRepository $robotsRepository)
    {
    }

    public function getRobotsDirectives(): iterable
    {
        yield RobotsDirective::forPath('Allow', '/');
        yield RobotsDirective::forPath('Disallow', '/admin');

        foreach ($this->robotsRepository->getActive() as $robots) {
            yield RobotsDirective::forPath($robots->getAction(), $robots->getPath());
        }
    }
}
