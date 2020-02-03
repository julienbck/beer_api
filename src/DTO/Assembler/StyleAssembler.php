<?php


namespace App\DTO\Assembler;


use App\DTO\StyleDTO;
use App\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;

class StyleAssembler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function hydrateEntity(StyleDTO $styleDTO)
    {
        $style = new Style();

        $style
            ->setName($styleDTO->getName())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $style;
    }
}