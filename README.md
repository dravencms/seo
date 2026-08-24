# DravenCMS SEO

SEO administration and frontend integration for DravenCMS applications. The package provides editable `robots.txt` directives, XML sitemap routes, and reusable tracking-code templates that can be placed in the document header or at the end of the body.

## Features

- Administration for `robots.txt` directives.
- XML sitemap and sitemap stylesheet endpoints.
- Tracking-service templates with per-site identifiers.
- Header and footer tracking-code placement.
- DravenCMS admin menu and ACL fixtures.
- Doctrine ORM attribute mappings for the package entities.

## Requirements

- PHP version supported by the installed DravenCMS stack.
- DravenCMS Admin 2.3 or newer.
- DravenCMS Locale 2.1 or newer.
- Nette DI 3.2.6 or newer.
- DravenCMS Structure is needed by the bundled sitemap and `robots.txt` presenters.

## Installation

Install the package with Composer:

```bash
composer require dravencms/seo
```

The DravenCMS package loader reads `dravencms.config.neon` from the package metadata. When integrating the package without that loader, include the file from your application configuration, adjusting the relative path as needed:

```neon
includes:
    - ../vendor/dravencms/seo/dravencms.config.neon
```

The package configuration registers its DI extension, routes, presenters, services, and Doctrine mappings. Apply the resulting database schema changes through the Doctrine migration workflow used by your application.

Load the package fixtures when you need the default admin menu, ACL resources and operations, and bundled tracking-service templates. The supplied templates include common services such as Google Analytics, Facebook Pixel, Hotjar, and Sklik.

## Frontend Integration

Add `TSeoPresenter` to a presenter shared by the frontend pages that should render tracking code:

```php
<?php declare(strict_types = 1);

namespace App\FrontModule;

use Dravencms\BasePresenter;
use Dravencms\Seo\TSeoPresenter;

abstract class FrontPresenter extends BasePresenter
{
    use TSeoPresenter;
}
```

Render tracking services in the appropriate places in the application layout:

```latte
<head>
    {* Other head content *}
    {control seoTracking:header}
</head>
<body>
    {include content}

    {control seoTracking:footer}
</body>
```

`seoTracking:header` renders services configured for the `header` position. `seoTracking:footer` renders services configured for the `bodyBottom` position.

## Tracking Templates

A tracking service defines a reusable HTML or JavaScript template. Insert `%IDENTIFIER%` wherever a tracking instance's identifier should be substituted. For example:

```html
<script>
    analytics.initialize('%IDENTIFIER%');
</script>
```

Create a tracking entry in the administration, select the service, and enter the provider-specific identifier. The frontend component replaces every `%IDENTIFIER%` placeholder before rendering the template.

Tracking templates are intentionally rendered without escaping because they contain executable markup. Only trusted administrators should be allowed to create or edit them.

## Public Routes

The package adds these frontend routes:

| Path | Purpose |
| --- | --- |
| `/robots.txt` | Active robots directives and structure exclusions |
| `/sitemap.xml` | XML sitemap generated from DravenCMS Structure |
| `/sitemap.xsl` | Stylesheet used to display the XML sitemap |

## Administration and Permissions

The package provides administration screens for robots directives, tracking entries, and tracking-service templates. Its fixtures define the `seo` ACL resource with these operations:

- `edit` and `delete`
- `robotsEdit` and `robotsDelete`
- `trackingEdit` and `trackingDelete`

The administrator group receives these permissions when the fixtures are loaded.

## License

This package is licensed under the LGPL-3.0-only license.
