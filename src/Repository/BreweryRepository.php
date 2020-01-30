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
}
