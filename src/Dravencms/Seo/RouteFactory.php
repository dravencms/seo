<?php

namespace Dravencms\Seo;

use Dravencms\Base\IRouterFactory;
use Nette\Application\Routers\RouteList;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class RouteFactory implements IRouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter(): RouteList
    {
        $router = new RouteList();

        $frontEnd = new RouteList('Front');

        $frontEnd->addRoute('sitemap.xml', 'Seo:Sitemap:default');
        $frontEnd->addRoute('sitemap.xsl', 'Seo:Sitemap:stylesheet');
        $frontEnd->addRoute('robots.txt', 'Seo:Robots:default');

        $router->add($frontEnd);
        
        return $router;
    }
}