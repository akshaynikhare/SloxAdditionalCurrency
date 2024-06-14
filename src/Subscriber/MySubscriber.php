<?php

declare(strict_types=1);

namespace SloxAdditionalCurrency\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\ProductEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use SloxAdditionalCurrency\Struct\AdditionalCurrencyStruct;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Storefront\Page\Page;
use Shopware\Storefront\Page\PageLoadedEvent;
use Shopware\Storefront\Page\GenericPageLoadedEvent;

use Shopware\Core\System\SystemConfig\SystemConfigService;



class MySubscriber implements EventSubscriberInterface
{

    private $container;
    private $systemConfigService;

    public function __construct(ContainerInterface $container, systemConfigService $systemConfigService)
    {
        $this->container = $container;
        $this->systemConfigService = $systemConfigService;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender',
            GenericPageLoadedEvent::class => 'onPageLoaded'
        ];
    }


    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $config = $this->systemConfigService->get('SloxAdditionalCurrency.config');
        // Extract the currency conversion rates from the config
        $currencyRates = [];
        foreach ($config as $key => $value) {
            if (strpos($key, 'euroTO') === 0) {
                $currency = substr($key, 6);
                if (!isset($currencyRates[$currency])) {
                    $currencyRates[strtoupper($currency)] = $value;
                }
            }
        }

        // Extract the currency import URL from the config
        $lastUpdate = isset($config['lastUpdate']) ? $config['lastUpdate'] : strtotime('-2 day');
        $currencyImportUrl = isset($config['currencyImportUrl']) ? $config['currencyImportUrl'] : null;


        $xmlData = file_get_contents($currencyImportUrl);

        // Parse the XML data
        $xml = simplexml_load_string($xmlData);

        // Extract the time from the XML
        $xmlTime = strtotime('+1 day');
        if (isset($xml->Cube->Cube['time'])) {
            $xmlTime = strtotime((string) $xml->Cube->Cube['time']);
        }

        foreach ($xml->Cube->Cube->Cube as $cube) {
            if (isset($cube['currency']) && isset($cube['rate'])) {
                $currency = (string) $cube['currency'];
                $rate = (float) $cube['rate'];
                if (isset($currencyRates[strtoupper($currency)])) {
                    $currencyRates[strtoupper($currency)] = $rate;
                }
            }
        }


        if ($lastUpdate < strtotime('-1 day') && $lastUpdate < $xmlTime) {

            foreach ($currencyRates as $currency => $rate) {
                $item = $this->systemConfigService->get('SloxAdditionalCurrency.config.' . 'euroTO' . strtolower($currency));
                if (isset($item)) {
                    $this->systemConfigService->set('SloxAdditionalCurrency.config.' . 'euroTO' . strtolower($currency), (float) $rate);
                }
            }
        }

 
       
    }


    public function onStorefrontRender(StorefrontRenderEvent $event): void
    {

        // Get the additional currency from the session or browser cookie
        $additionalCurrency = $this->getAdditionalCurrency();

        // Pass the additional currency to the Twig template for rendering
        $this->addAdditionalCurrencyToTwig($event, $additionalCurrency);
    }

    private function getAdditionalCurrency()
    {
        // Check if the session variable exists
        if (isset($_SESSION['additional_currency'])) {
            return new AdditionalCurrencyStruct($_SESSION['additional_currency']);
        }

        // Check if the browser cookie exists
        if (isset($_COOKIE['additional_currency'])) {
            return new AdditionalCurrencyStruct($_COOKIE['additional_currency']);
        }

        // Return a default Struct if neither the session variable nor the cookie is set
        return new AdditionalCurrencyStruct('USD');
    }


    private function addAdditionalCurrencyToTwig($event, $additionalCurrency)
    {
        $config = $this->systemConfigService->get('SloxAdditionalCurrency.config');
        

        // Extract the currency conversion rates from the config
        $currencyRates = [];
        foreach ($config as $key => $value) {
            if (strpos($key, 'euroTO') === 0) {
                $currency = substr($key, 6);
                if (!isset($currencyRates[$currency])) {
                    $currencyRates[strtoupper($currency)] = $value;
                }
            }
        }

        $additionalCurrency->setAdditionalList($currencyRates);
        $event->getSalesChannelContext()->addExtension('additional_currency', $additionalCurrency);
    }
}
