<?php declare(strict_types = 1);

namespace Dravencms\Seo\Robots;

final class RobotsCollector
{
    /** @var list<RobotsProviderInterface> */
    private array $providers = [];

    public function addProvider(RobotsProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /** @return list<RobotsDirective> */
    public function getDirectives(): array
    {
        $directives = [];

        foreach ($this->providers as $provider) {
            foreach ($provider->getRobotsDirectives() as $directive) {
                $directives[] = $directive;
            }
        }

        return $directives;
    }
}
