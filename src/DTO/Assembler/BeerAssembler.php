<?php


namespace App\DTO\Assembler;


use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;

class BeerAssembler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function hydrateEntity($beerDTO): Beer
    {
        $beer = new Beer();
        $beer
            ->setName($beerDTO->getName())
            ->setIbu($beerDTO->getIbu())
            ->setAbv($beerDTO->getAbv())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        if (!empty($beerDTO->getBreweryId())) {
            $brewery = $this->em->getRepository(Brewery::class)->find($beerDTO->getBreweryId());
            $beer->setBrewery($brewery);
        }

        if (!empty($beerDTO->getStyleId())) {
            $style = $this->em->getRepository(Style::class)->find($beerDTO->getStyleId());
            $beer->setStyle($style);
        }

        return $beer;
    }
}