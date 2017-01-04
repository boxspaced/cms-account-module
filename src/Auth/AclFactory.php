<?php
namespace Account\Auth;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Permissions\Acl\Role\GenericRole as Role;

class AclFactory implements FactoryInterface
{

    /**
     * @var Acl
     */
    private $acl;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Acl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = isset($config['acl']) ? $config['acl'] : [];

        $this->acl = new Acl();

        $this->addResources($config);
        $this->addRoles($config);

        $this->acl->addRole(new Role('admin'), $this->acl->getRoles());

        $this->addRules($config);

        return $this->acl;
    }

    /**
     * @param array $config
     * @return AclFactory
     */
    protected function addResources(array $config)
    {
        if (!isset($config['resources'])) {
            return $this;
        }

        foreach ((array) $config['resources'] as $resource) {

            if (!isset($resource['id'])) {
                continue;
            }

            $this->acl->addResource(new Resource($resource['id']));
        }

        return $this;
    }

    /**
     * @param array $config
     * @return AclFactory
     */
    protected function addRoles(array $config)
    {
        if (!isset($config['roles'])) {
            return $this;
        }

        foreach ((array) $config['roles'] as $role) {

            if (!isset($role['id'])) {
                continue;
            }

            $parents = isset($role['parents']) ? $role['parents'] : null;

            $this->acl->addRole(new Role($role['id']), $parents);
        }

        return $this;
    }

    /**
     * @param array $config
     * @return AclFactory
     */
    protected function addRules(array $config)
    {
        if (!isset($config['rules'])) {
            return $this;
        }

        $acl = $this->acl;

        foreach ((array) $config['rules'] as $rule) {

            if (!isset($rule['type'])) {
                continue;
            }

            $roles = isset($rule['roles']) ? $rule['roles'] : null;
            $resources = isset($rule['resources']) ? $rule['resources'] : null;
            $privileges = isset($rule['privileges']) ? $rule['privileges'] : null;

            if ($acl::TYPE_ALLOW === $rule['type']) {
                $this->acl->allow($roles, $resources, $privileges);
            }

            if ($acl::TYPE_DENY === $rule['type']) {
                $this->acl->deny($roles, $resources, $privileges);
            }
        }

        return $this;
    }

}
