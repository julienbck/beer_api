<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BeerDTO
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    protected $name;

    /**
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     * @var integer
     */
    protected $abv;

    /**
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     * @var integer
     */
    protected $ibu;

    /**
     * @Assert\Type("integer")
     * @var integer
     */
    protected $breweryId = null;

    /**
     * @Assert\Type("integer")
     * @var integer
     */
    protected $styleId = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BeerDTO
     */
    public function setName(string $name): BeerDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbv(): int
    {
        return $this->abv;
    }

    /**
     * @param int $abv
     * @return BeerDTO
     */
    public function setAbv(int $abv): BeerDTO
    {
        $this->abv = $abv;
        return $this;
    }

    /**
     * @return int
     */
    public function getIbu(): int
    {
        return $this->ibu;
    }

    /**
     * @param int $ibu
     * @return BeerDTO
     */
    public function setIbu(int $ibu): BeerDTO
    {
        $this->ibu = $ibu;
        return $this;
    }

    /**
     * @return int
     */
    public function getBreweryId(): ?int
    {
        return $this->breweryId;
    }

    /**
     * @param int $breweryId
     * @return BeerDTO
     */
    public function setBreweryId(int $breweryId): BeerDTO
    {
        $this->breweryId = $breweryId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStyleId(): ?int
    {
        return $this->styleId;
    }

    /**
     * @param mixed $styleId
     * @return BeerDTO
     */
    public function setStyleId($styleId)
    {
        $this->styleId = $styleId;
        return $this;
    }
}