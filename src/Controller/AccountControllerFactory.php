<?php
namespace Boxspaced\CmsAccountModule\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsAccountModule\Controller\AccountController;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Zend\Log\Logger;
use Boxspaced\CmsCoreModule\Controller\AbstractControllerFactory;

class AccountControllerFactory extends AbstractControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AccountController(
            $container->get(AccountService::class),
            $container->get(Logger::class)
        );

        $this->adminNavigationWidget($controller, $container);

        return $this->forceHttps($controller, $container);
    }

}
