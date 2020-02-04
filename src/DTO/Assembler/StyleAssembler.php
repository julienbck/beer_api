<?php


namespace App\DTO\Assembler;


use App\DTO\StyleDTO;
use App\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function hydrateEntityPatch(StyleDTO $styleDTO, Style $style): Style
    {
        if (empty($styleDTO->getName())) {
            throw new BadRequestHttpException('Need name with string value to update style');
        }

        $style->setName($styleDTO->getName());
        $style->setUpdatedAt(new \DateTime());

        return $style;
    }
}