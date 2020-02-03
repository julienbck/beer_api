<?php


namespace App\DTO\Assembler;


use App\DTO\BreweryDTO;
use App\Entity\Brewery;
use Doctrine\ORM\EntityManagerInterface;

class BreweryAssembler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function hydrateEntity(BreweryDTO $breweryDTO): Brewery
    {
        $brewery = new Brewery();

        $brewery
            ->setName($breweryDTO->getName())
            ->setAddress($breweryDTO->getAddress())
            ->setCity($breweryDTO->getCity())
            ->setPostalCode($breweryDTO->getPostalCode())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $brewery;
    }
}