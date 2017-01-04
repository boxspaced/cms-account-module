<?php
namespace Account\Auth;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Account\Controller\AccountController;

class Acl extends ZendAcl
{

    public function __construct()
    {
        $this->addResource(new Resource(AccountController::class));

        $this->addRole(new Role('guest'));
        $this->addRole(new Role('authenticated-user'), 'guest');
        $this->addRole(new Role('author'), 'authenticated-user');
        $this->addRole(new Role('publisher'), 'authenticated-user');

        $this->allow('guest', AccountController::class, [
            'login',
            'logout',
            'access-denied',
        ]);

        $this->allow('authenticated-user', AccountController::class, [
            'index',
        ]);
    }

    /**
     * @param string[] $roles
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function isAllowedMultiRoles(array $roles, $resource, $action)
    {
        foreach ($roles as $role) {

            if ($this->isAllowed($role, $resource, $action)) {
                return true;
            }
        }

        return false;
    }

}
