<?php


namespace App\DTO\Assembler;


use App\DTO\BeerDTO;
use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            if (empty($brewery)) {
                throw new NotFoundHttpException('Not found brewery with id '.$beerDTO->getBreweryId());
            }
            $beer->setBrewery($brewery);
        }

        if (!empty($beerDTO->getStyleId())) {
            $style = $this->em->getRepository(Style::class)->find($beerDTO->getStyleId());
            if (empty($style)) {
                throw new NotFoundHttpException('Not found style with id '.$beerDTO->getStyleId());
            }
            $beer->setStyle($style);
        }

        return $beer;
    }

    public function hydrateEntityPatch(BeerDTO $beerDTO, Beer $beer): Beer
    {

        if (!is_null($beerDTO->getName())) {
            $beer->setName($beerDTO->getName());
        }

        if (!is_null($beerDTO->getIbu())) {
            $beer->setIbu($beerDTO->getIbu());
        }

        if (!is_null($beerDTO->getAbv())) {
            $beer->getAbv($beerDTO->getAbv());
        }

        if (!empty($beerDTO->getBreweryId())) {
            $brewery = $this->em->getRepository(Brewery::class)->find($beerDTO->getBreweryId());
            $beer->setBrewery($brewery);
        }

        if (!empty($beerDTO->getStyleId())) {
            $style = $this->em->getRepository(Style::class)->find($beerDTO->getStyleId());
            $beer->setStyle($style);
        }
        $beer->setUpdatedAt(new \DateTime());

        return $beer;
    }

}