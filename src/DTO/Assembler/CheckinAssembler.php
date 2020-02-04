<?php


namespace App\DTO\Assembler;


use App\DTO\CheckinDTO;
use App\Entity\Beer;
use App\Entity\Checkin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

class CheckinAssembler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CheckinAssembler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function hydrateEntity(CheckinDTO $checkinDTO)
    {
        try {
            $beer = $this->em->getRepository(Beer::class)->find($checkinDTO->getBeerId());

            if (empty($beer)) {
                throw new NotFoundHttpException('Don\'t find beer with id '.$checkinDTO->getBeerId());
            }

            $checkin = new Checkin();

            $checkin
                ->setNote($checkinDTO->getNote())
                ->setBeer($beer)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            return $checkin;
        } catch (NotFoundResourceException $e) {
            return new Response($e->getMessage(), 404);
        }

    }
}