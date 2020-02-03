<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;


class StyleDTO
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @var string
     */
    protected  $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return StyleDTO
     */
    public function setName(string $name): StyleDTO
    {
        $this->name = $name;
        return $this;
    }
}