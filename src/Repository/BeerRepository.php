<?php

namespace App\Repository;

use App\Entity\Beer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Beer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beer[]    findAll()
 * @method Beer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beer::class);
    }

    public function getCollection($requestQuery)
    {
        $sortDescAttribute = $requestQuery->get('sort_desc_by_attribute');
        $sortAscAttribute = $requestQuery->get('sort_asc_by_attribute');

        $qb = $this->createQueryBuilder('b');

        $qb
            ->addSelect('br, s')
            ->join('b.brewery', 'br')
            ->leftJoin('b.style', 's');

        if ((empty($sortDescAttribute) && empty($sortAscAttribute))  || (!empty($sortDescAttribute) && !empty($sortAscAttribute))) {
            $qb->orderBy('b.id', 'ASC');
        }

        if (!empty($sortDescAttribute) && empty($sortAscAttribute)) {
            $qb->orderBy('b.'.$sortDescAttribute, 'DESC');
        }


        if (!empty($sortAscAttribute) && empty($sortDescAttribute)) {
            $qb->orderBy('b.'.$sortAscAttribute, 'DESC');
        }

        return $qb->getQuery();
    }

    public function getOneById(int $id): ?Beer
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->addSelect('br, s')
            ->join('b.brewery', 'br')
            ->leftJoin('b.style', 's')
            ->where('b.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getBeerByMaxAttribute($attributeName)
    {
        $qb = $this->createQueryBuilder('b');
        $qb2 = $this->_em->createQueryBuilder();

        $qb
            ->addSelect('br, s')
            ->join('b.brewery', 'br')
            ->leftJoin('b.style', 's')
            ->where(
                $qb->expr()->in(
                    sprintf('b.%s', $attributeName),
                    $qb2
                        ->select(sprintf('MAX(b2.%s)', $attributeName))
                            ->from(Beer::class, 'b2')
                            ->getDQL()
                        )
                );

        return $qb->getQuery()->getResult();
    }
}
