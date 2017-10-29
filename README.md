LaraMod Admin LogViewer 0.* Alpha
----------------------------
LaraMod is a modular Laravel based CMS.
https://github.com/LaraModulus

**WARNING: Until v1 there will be no backward compatibility and some versions may require migrate:refresh** 

Installation
---------------
```
composer require laramod\admin-logviewer
```
 **config/app.php**
 
```php 
'providers' => [
    ...
    LaraMod\Admin\Logs\AdminLogViewerServiceProvider::class,
]
```

In `config/admincore.php` you can edit admin menu

**DEMO:** http://laramod.novaspace.eu/admin
```
user: admin@admin.com
pass: admin