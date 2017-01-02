<?php
namespace Account\Auth;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as Adapter;
use Boxspaced\EntityManager\EntityManager;

class AuthenticationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new AuthenticationService();

        $adapter = new Adapter($container->get(EntityManager::class)->getDb());
        $adapter->setTableName('user');
        $adapter->setIdentityColumn('username');
        $adapter->setCredentialColumn('password');
        $service->setAdapter($adapter);

        return $service;
    }

}
