# ISSyliusEcommpayPlugin

## Installation

```bash
$ composer require infinite-software/sylius-ecommpay-plugin
```
    
Add plugin dependencies to your app/AppKernel.php file:
```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new IS\SyliusEcommpayPlugin\ISSyliusEcommpayPlugin(),
    ]);
}
```

Add new Payment Method in your project Admin section with Ecommpay gateway, where you should add provided `secretkey` and `project_id`.

Ask Ecommpay Support to set Callback URL to `http://example.com/payment/notify/unsafe/ecommpay` (or reassign route from this action to another place if needed)