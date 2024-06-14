<?php declare(strict_types=1);

namespace SloxAdditionalCurrency\Controller;

use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannel\AbstractContextSwitchRoute;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Shopware\Storefront\Framework\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Shopware\Core\System\SalesChannel\ContextTokenResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class SloxConfigController extends StorefrontController
{
    #[Route(
        path: '/sloxconfig',
        name: 'frontend.sloxadditionalcurrency.config',
        options: ['seo' => false], 
        defaults: ['XmlHttpRequest' => true], 
        methods: ['POST'])]
    public function sloxConfig(Request $request, SalesChannelContext $context): Response
    {
        $referer = $request->headers->get('referer');
        $additionalCurrency = $request->request->get('additionalcurrency');

        if(!isset($additionalCurrency)){
            $additionalCurrency = 'USD';
        }
        setcookie('additional_currency',  $additionalCurrency, time() + (86400 * 30), "/");
        $_SESSION['additional_currency'] =  $additionalCurrency;
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }
   
}
