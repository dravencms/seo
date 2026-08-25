<?php declare(strict_types = 1);

namespace Dravencms\Seo\DI;

use Dravencms\Seo\Robots\DatabaseRobotsProvider;
use Dravencms\Seo\Robots\RobotsCollector;
use Dravencms\Seo\Robots\RobotsProviderInterface;
use Dravencms\Seo\Seo;
use Dravencms\Seo\Sitemap\SitemapCollector;
use Dravencms\Seo\Sitemap\SitemapProviderInterface;
use Nette\DI\CompilerExtension;

use Salamek\Cms\DI\CmsExtension;
/**
 * Class SeoExtension
 * @package Dravencms\Seo\DI
 */
class SeoExtension extends CompilerExtension
{

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('seo'))
            ->setFactory(Seo::class);

        $builder->addDefinition($this->prefix('robotsCollector'))
            ->setFactory(RobotsCollector::class);

        $builder->addDefinition($this->prefix('sitemapCollector'))
            ->setFactory(SitemapCollector::class);

        $builder->addDefinition($this->prefix('databaseRobotsProvider'))
            ->setFactory(DatabaseRobotsProvider::class);

        if (class_exists(CmsExtension::class)) {
            $this->loadCmsComponents();
            $this->loadCmsModels();
        }

        $this->loadComponents();
        $this->loadModels();
        $this->loadConsole();
    }

    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();
        $robotsCollector = $builder->getDefinition($this->prefix('robotsCollector'));
        $sitemapCollector = $builder->getDefinition($this->prefix('sitemapCollector'));

        foreach ($builder->findByType(RobotsProviderInterface::class) as $serviceName => $service) {
            $robotsCollector->addSetup('addProvider', ['@' . $serviceName]);
        }

        foreach ($builder->findByType(SitemapProviderInterface::class) as $serviceName => $service) {
            $sitemapCollector->addSetup('addProvider', ['@' . $serviceName]);
        }
    }
    
    protected function loadCmsModels(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/cmsModels.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('cmsModels.' . $i));
            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadCmsComponents(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/cmsComponents.neon') as $i => $command) {
            $cli = $builder->addFactoryDefinition($this->prefix('cmsComponent.' . $i))
                ->addTag(CmsExtension::TAG_COMPONENT);
            if (is_string($command)) {
                $cli->setImplement($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadComponents(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/components.neon') as $i => $command) {
            $cli = $builder->addFactoryDefinition($this->prefix('components.' . $i));
            if (is_string($command)) {
                $cli->setImplement($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadModels(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/models.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('models.' . $i));
            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadConsole(): void
    {
        $builder = $this->getContainerBuilder();

        foreach ($this->loadFromFile(__DIR__ . '/console.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('cli.' . $i))
                ->setAutowired(false);

            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }
}
