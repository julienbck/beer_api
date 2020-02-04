<?php

namespace App\Repository;

use App\Entity\Brewery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Brewery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brewery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brewery[]    findAll()
 * @method Brewery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreweryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brewery::class);
    }

    public function getCollection()
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->orderBy('b.id', 'ASC');

        return $qb->getQuery();
    }

    public function getOneById(int $id): ?Brewery
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->where('b.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getNumberBreweryByCountry($sortVal)
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->select('b.country, COUNT(b.id) as totalBreweries')
            ->orderBy('totalBreweries', $sortVal ? $sortVal : "DESC")
            ->groupBy('b.country');
        
        return $qb->getQuery()->getScalarResult();
    }
}
