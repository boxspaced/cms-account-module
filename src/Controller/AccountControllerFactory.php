<?php
namespace Boxspaced\CmsAccountModule\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsAccountModule\Controller\AccountController;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Zend\Log\Logger;

class AccountControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AccountController(
            $container->get(AccountService::class),
            $container->get(Logger::class)
        );
    }

}
