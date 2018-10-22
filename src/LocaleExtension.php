<?php

declare(strict_types=1);

namespace IS\SyliusEcommpayPlugin;

use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Convert;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocaleExtension implements ExtensionInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $thankYouRoute;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param RouterInterface $router
     * @param string $thankYouRoute
     */
    public function __construct(RouterInterface $router, string $thankYouRoute)
    {
        $this->router = $router;
        $this->thankYouRoute = $thankYouRoute;
    }


    public function onPreExecute(Context $context)
    {

    }

    public function onExecute(Context $context)
    {
        $previousStack = $context->getPrevious();
        $previousStackSize = count($previousStack);

        if ($previousStackSize !== 2) {
            return;
        }
        $request = $context->getRequest();
        if (!$request instanceof GetStatus) {
            return;
        }
        $this->locale = $request->getFirstModel()->getOrder()->getLocaleCode();
    }

    public function onPostExecute(Context $context)
    {
        $previousStack = $context->getPrevious();
        $previousStackSize = count($previousStack);

        if ($previousStackSize !== 2) {
            return;
        }
        $request = $context->getRequest();
        if (!$context->getRequest() instanceof Convert) {
            return;
        }

        $result = $request->getResult();
        $result['merchant_success_url'] = $this->router->generate(
            $this->thankYouRoute,
            ['_locale' => $this->locale],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $result['language_code'] = $this->locale;
        $request->setResult($result);
    }
}
