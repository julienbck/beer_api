<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getCollection()
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->orderBy('u.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getOneById($id)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->addSelect('c, b')
            ->leftJoin('u.checkins', 'c')
            ->leftJoin('c.beer', 'b')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
