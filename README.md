# Blesta #

Blesta is a well-written, security-focused, user and developer-friendly client
management, billing, and support application.

## Minimum Requirements ##

* PHP version 5.4
* PDO, pdo_mysql, curl (version 7.10.5), and openssl (version 0.9.6) PHP extensions.
* MySQL version 5.0.17
* Apache, IIS, or LiteSpeed Web Server
* ionCube PHP loader

### Using hotfix files for ionCube
If you are running PHP older than version 7.3, you may need to overwrite files
in your Blesta installation with the hotfix files provided. Whether this is
necessary is dependent upon your version of ionCube and PHP as described below.

**NOTE: A hotfix is necessary if you receive an error like:**

* The file /blesta/app/app_controller.php is corrupted.
* The file /blesta/app/models/license.php is corrupted.

ionCube loader version:

* ionCube version >= 10.1.0 - no hotfix needs to be applied
* ionCube version < 10.1.0 - **a hotfix will need to be applied**

The hotfix to use depends on your version of PHP.

PHP version:

* PHP version >= 7.1.0 - use _/hotfix-php71/blesta/_
* PHP version >= 5.6.0 and < 7.1.0 - use _/hotfix-php7/blesta/_
* PHP version >= 5.4.0 and < 5.6.0 - use _/hotfix-php54/blesta/_

Apply the hotfix by overwriting the files in your Blesta installation
(i.e. within the _/blesta/_ directory) with the files from the hotfix directory.

Afterward, proceed with the installation below.

For recommended requirements and additional information, please see the
[documentation](http://docs.blesta.com/display/user/Requirements).

## Installation ##

To install, upload the contents of blesta to your web server and visit this
location in your browser.

For more detailed instructions, please see the
[documentation](http://docs.blesta.com/display/user/Installing+Blesta) for
installing Blesta.

## Upgrading ##

Note! Back up your database and files before beginning an upgrade.

To upgrade, overwrite the files in your existing installation and access
~/admin/upgrade in your browser.

For more detailed instructions, please see the
[documentation](http://docs.blesta.com/display/user/Upgrading+Blesta) for
upgrading Blesta.

## Patching ##

Note! Back up your database and files before applying a patch.

Patches contain all patches issued for the minor release. For example, a patch
labeled 3.0.6 will contain all patches issued from 3.0.1, so it is not necessary
to apply patches incrementally.

To patch your installation, overwrite the files in your existing installation
and access ~/admin/upgrade in your browser.

For more detailed instructions, please see the
[documentation](http://docs.blesta.com/display/user/Upgrading+Blesta#UpgradingBlesta-Patchinganexistinginstall)
for patching Blesta.

