<?php
namespace Account\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Account\Controller\AccountController;
use Account\Service\AccountService;
use Zend\Log\Logger;
use Core\Controller\AbstractControllerFactory;

class AccountControllerFactory extends AbstractControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AccountController(
            $container->get(AccountService::class),
            $container->get(Logger::class)
        );

        return $this->forceHttps($controller, $container);
    }

}
