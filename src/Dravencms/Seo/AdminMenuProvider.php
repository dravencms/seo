<?php declare(strict_types=1);

namespace Dravencms\Seo;

use Dravencms\Admin\Menu\{AdminMenuItem, AdminMenuProviderInterface};

final class AdminMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('seo', 'seo.menu.root', icon: 'fa-binoculars', order: 600);
        yield new AdminMenuItem('seo.robots', 'seo.menu.robots', destination: ':Admin:Seo:Robots:default', parent: 'seo', icon: 'fa-fire', order: 10, resource: 'seo', privilege: 'robotsEdit');
        yield new AdminMenuItem('seo.tracking', 'seo.menu.tracking', destination: ':Admin:Seo:Tracking:default', parent: 'seo', icon: 'fa-line-chart', order: 20, resource: 'seo', privilege: 'trackingEdit');
        yield new AdminMenuItem('seo.trackingServices', 'seo.menu.trackingServices', destination: ':Admin:Seo:TrackingService:default', parent: 'seo', icon: 'fa-cog', order: 30, resource: 'seo', privilege: 'trackingEdit');
    }
}
