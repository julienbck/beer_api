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

    public function hydrateEntityPatch(BreweryDTO $breweryDTO, Brewery $brewery): Brewery
    {
        if (!is_null($breweryDTO->getName())) {
             $brewery->setName($breweryDTO->getName());
        }

        if (!is_null($breweryDTO->getAddress())) {
            $brewery->setAddress($breweryDTO->getAddress());
        }

        if (!is_null($breweryDTO->getPostalCode())) {
            $brewery->setPostalCode($breweryDTO->getPostalCode());
        }

        if (!is_null($breweryDTO->getCity())) {
            $brewery->setCity($breweryDTO->getCity());
        }

        if (!is_null($breweryDTO->getCountry())) {
            $brewery->setCountry($breweryDTO->getCountry());
        }

        $brewery->setUpdatedAt(new \DateTime());

        return $brewery;
    }
}