eZ Tags Bundle installation instructions
========================================

Requirements
------------

* eZ Publish 5.0+
* eZ Publish Legacy Stack with legacy eZ Tags extension installed and configured

Installation steps
------------------

### Clone the repo

This bundle is not yet available through Composer/Packagist, so you will have to clone it manually for now.

Clone the repo into `src/EzSystems/TagsBundle` folder of your eZ Publish 5 installation.

```bash
mkdir src/EzSystems
cd src/EzSystems
git clone http://path.to.repo/... TagsBundle
```

### Activate the bundle

Activate the bundle in `ezpublish\EzPublishKernel.php` file.

```php
use EzSystems\TagsBundle\EzSystemsTagsBundle;

...

public function registerBundles()
{
   $bundles = array(
       new FrameworkBundle(),
       ...
       new EzSystemsTagsBundle()
   );

   ...
}
```

### Edit configuration

Put the following config in your `ezpublish/config.yml` file to be able to load `eztags` content field template.

```yml
parameters:
   ezsettings.YOUR_SITEACCESS_NAME.field_templates:
       - {template: EzPublishCoreBundle::content_fields.html.twig, priority: 0}
       - {template: eZTagsBundle::eztags_content_field.html.twig, priority: 0}
```

Be sure to replace `YOUR_SITEACCESS_NAME` text with the name of your frontend siteaccess.

### Clear the caches

Clear eZ Publish 5 caches.

```bash
php ezpublish/console cache:clear
```