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

## Adding Payment Page extra parameters

If you need prepend parameters before sending request to Ecommpay (for example from https://developers.ecommpay.com/ru/ru_PP_Parameters.html),
copy contents of `Payum\Ecommpay\Action\ConvertPaymentAction` class into a new file located in `src/Payment/Ecommpay/ConvertPaymentAction`:

```php
namespace App\Payment\Ecommpay;

//use ...;

final class ConvertPaymentAction implements ActionInterface, ApiAwareInterface
{
    ...

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        /** @var OrderInterface $order */
        $params = [
            'payment_id' => $payment->getNumber(),
            'payment_amount' => $payment->getTotalAmount(),
            'payment_currency' => $payment->getCurrencyCode(),
            'project_id' => $this->api['projectId'],
            'customer_id' => $payment->getClientId(),
            // my extra parameter
            'force_payment_method' => 'card'
        ];
        $request->setResult($params);
    }
    ...
}

```
And lastly declare it as service in `services.yaml`. Do not forget to make service `public` like this:
```yaml
App\Payment\Ecommpay\ConvertPaymentAction:
    public: true
    tags:
        - { name: payum.action, factory: ecommpay, alias: payum.action.convert_payment }
```
