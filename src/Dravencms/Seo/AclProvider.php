<?php declare(strict_types=1);

namespace Dravencms\Seo;

use Dravencms\User\Acl\{AclPermission, AclProviderInterface};

final class AclProvider implements AclProviderInterface
{
    public function getPermissions(): iterable
    {
        yield new AclPermission('seo', 'edit', 'seo.acl.permissions.edit.label', resourceLabel: 'seo.acl.resource');
        yield new AclPermission('seo', 'delete', 'seo.acl.permissions.delete.label', resourceLabel: 'seo.acl.resource');
        yield new AclPermission('seo', 'robotsEdit', 'seo.acl.permissions.robotsEdit.label', resourceLabel: 'seo.acl.resource');
        yield new AclPermission('seo', 'robotsDelete', 'seo.acl.permissions.robotsDelete.label', resourceLabel: 'seo.acl.resource');
        yield new AclPermission('seo', 'trackingEdit', 'seo.acl.permissions.trackingEdit.label', resourceLabel: 'seo.acl.resource');
        yield new AclPermission('seo', 'trackingDelete', 'seo.acl.permissions.trackingDelete.label', resourceLabel: 'seo.acl.resource');
    }
}
