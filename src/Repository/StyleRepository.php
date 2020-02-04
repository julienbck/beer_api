<?php

namespace App\Repository;

use App\Entity\Style;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Style|null find($id, $lockMode = null, $lockVersion = null)
 * @method Style|null findOneBy(array $criteria, array $orderBy = null)
 * @method Style[]    findAll()
 * @method Style[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StyleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Style::class);
    }

    public function getCollection()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->orderBy('s.name', 'ASC');

        return $qb->getQuery();
    }

    public function getOneById(int $id): ?Style
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->where('b.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getNumberBeersByStyle($sortVal)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->select('s.id, s.name, COUNT(b.id) as totalBreweries')
            ->join('s.beers', 'b')
            ->orderBy('totalBreweries', $sortVal ? $sortVal : "DESC")
            ->groupBy('s.id');

        return $qb->getQuery()->getScalarResult();
    }
}
