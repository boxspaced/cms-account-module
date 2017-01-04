<?php
namespace Account\Service;

use Zend\Authentication\AuthenticationService;
use Account\Auth\Acl;
use Zend\Db\Adapter\Adapter as Database;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\DashToCamelCase;

class AccountService
{

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param AuthenticationService $authService
     * @param Acl $acl
     * @param Database $db
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(
        AuthenticationService $authService,
        Acl $acl,
        Database $db,
        Logger $logger,
        array $config
    )
    {
        $this->authService = $authService;
        $this->acl = $acl;
        $this->db = $db;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param string $username
     * @param string $password
     * @return Identity|null
     */
    public function login($username, $password)
    {
        $this->authService->getAdapter()
            ->setIdentity($username)
            ->setCredential(hash($this->config['account']['password_hashing_algorithm'], $password));

        $result = $this->authService->authenticate();

        if (!$result->isValid()) {
            return null;
        }

        $data = $this->authService->getAdapter()->getResultRowObject(null, 'password');

        $sql = new Sql($this->db);

        $select = $sql->select();
        $select->from('user_role');
        $select->join('role', 'role.id = user_role.role_id');
        $select->where([
            'user_role.user_id = ?' => $data->id,
        ]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        $roles = $stmt->execute()->getResource()->fetchAll();

        $identity = new Identity();
        $identity->id = $data->id;
        $identity->username = $data->username;
        $identity->roles[] = 'authenticated-user';

        foreach ($roles as $role) {

            if ($this->acl->hasRole($role['name'])) {
                $identity->roles[] = $role['name'];
            }
        }

        $this->authService->getStorage()->write($identity);

        return $identity;
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->authService->clearIdentity();
    }

    /**
     * @return Identity|null
     */
    public function getIdentity()
    {
        return $this->authService->getIdentity();
    }

    /**
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public function isAllowed($resource, $action)
    {
        $identity = $this->getIdentity();

        $roles = isset($identity->roles) ? $identity->roles : ['guest'];

        if (!class_exists($resource)) {

            $resource = str_replace(
                '##',
                StaticFilter::execute($resource, DashToCamelCase::class),
                '##\\Controller\\##Controller'
            );
        }

        return $this->acl->isAllowedMultiRoles(
            $roles,
            $resource,
            $action
        );
    }

}
