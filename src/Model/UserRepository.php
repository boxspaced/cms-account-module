<?php
namespace Boxspaced\CmsAccountModule\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class UserRepository
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getById($id)
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(User::class);
    }

    /**
     * @param User $entity
     * @return UserRepository
     */
    public function delete(User $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
