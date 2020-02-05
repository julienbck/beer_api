<?php


namespace App\DTO\Assembler;


use App\DTO\CheckinDTO;
use App\Entity\Beer;
use App\Entity\Checkin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

class CheckinAssembler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * CheckinAssembler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $storage)
    {
        $this->em = $em;
        $this->storage = $storage;
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
                ->setUser($this->storage->getToken()->getUser())
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            return $checkin;
        } catch (NotFoundResourceException $e) {
            return new Response($e->getMessage(), 404);
        }
    }

    public function hydrateEntityPatch(CheckinDTO $checkinDTO, Checkin $checkin): Checkin
    {
        if (!is_null($checkinDTO->getBeerId())) {
            throw new BadRequestHttpException('you can\'t send beer_id to update checkin');
        }

        if (is_null($checkinDTO->getNote()) || ($checkinDTO->getNote() < 0) || ($checkinDTO->getNote() > 10)) {
            throw new BadRequestHttpException('Need value integer for note between 0 to 10');
        }

        if ($checkin->getUser()->getId() == $this->storage->getToken()->getUser()->getId()) {
            throw new AccessDeniedException('Not allowed to update checkins other member');
        }

        $checkin->setNote($checkinDTO->getNote());
        $checkin->setUpdatedAt(new \DateTime());

        return $checkin;
    }
}