<?php
namespace Boxspaced\CmsAccountModule\Service;

use Zend\Authentication\AuthenticationService;
use Boxspaced\CmsAccountModule\Auth\Acl;
use Zend\Db\Adapter\Adapter as Database;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\DashToCamelCase;
use Zend\Authentication\Adapter\DbTable as DbTableAdapter;

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
     * @var Sql
     */
    protected $sql;

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
        $this->sql = new Sql($db);
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
            ->setCredential($password);

        $result = $this->authService->authenticate();

        if (!$result->isValid()) {

            if (!isset($this->config['account']['password_hashing_algorithm'])) {
                return null;
            }

            $adapter = new DbTableAdapter($this->db);
            $adapter->setTableName('user');
            $adapter->setIdentityColumn('username');
            $adapter->setCredentialColumn('password');
            $adapter->setIdentity($username);
            $adapter->setCredential(hash($this->config['account']['password_hashing_algorithm'], $password));

            $this->authService->setAdapter($adapter);

            $result = $this->authService->authenticate();

            if (!$result->isValid()) {
                return null;
            }

            // Authenticated, but has an old password
            $update = $this->sql->update('user');
            $update->set([
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $update->where([
                'username = ?' => $username,
            ]);

            $stmt = $this->sql->prepareStatementForSqlObject($update);
            $stmt->execute();
        }

        $data = $this->authService->getAdapter()->getResultRowObject(null, 'password');

        $select = $this->sql->select();
        $select->from('user_role');
        $select->join('role', 'role.id = user_role.role_id');
        $select->where([
            'user_role.user_id = ?' => $data->id,
        ]);

        $stmt = $this->sql->prepareStatementForSqlObject($select);
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
