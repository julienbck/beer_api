<?php

namespace App\Repository;

use App\Entity\Checkin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Checkin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Checkin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Checkin[]    findAll()
 * @method Checkin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Checkin::class);
    }

    public function getCollection($requestQuery)
    {
        $qb = $this->createQueryBuilder('ci');

        $qb
            ->addSelect('u')
            ->join('ci.user', 'u')
            ->orderBy('ci.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getOneById($id)
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->addSelect('b')
            ->leftJoin('c.beer', 'b')
            ->where('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
