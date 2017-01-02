<?php
namespace Account\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\EntityManager\EntityManager;
use Zend\Authentication\AuthenticationService;
use Account\Auth\Acl;
use Zend\Log\Logger;

class AccountServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AccountService(
            $container->get(AuthenticationService::class),
            $container->get(Acl::class),
            $container->get(EntityManager::class)->getDb(),
            $container->get(Logger::class),
            $container->get('config')
        );
    }

}
