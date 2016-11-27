<?php

namespace Dravencms\Seo;

use Dravencms\Structure\IRouterFactory;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RouteFactory implements IRouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router = new RouteList();

        $router[] = $frontEnd = new RouteList('Front');

        $frontEnd[] = new Route('sitemap.xml', 'Seo:Sitemap:default');
        $frontEnd[] = new Route('sitemap.xsl', 'Seo:Sitemap:stylesheet');
        $frontEnd[] = new Route('robots.txt', 'Seo:Robots:default');

        return $router;
    }
}