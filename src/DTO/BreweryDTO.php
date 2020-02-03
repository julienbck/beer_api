<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BreweryDTO
{


    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @var string
     */
    protected $name;

    /**
     * @Assert\Type("string")
     * @var string
     */
    protected $address = null;

    /**
     * @Assert\Type("string")
     */
    protected $city = null;

    /**
     * @Assert\Type("string")
     * @var string
     */
    protected $postalCode = null;

    /**
     * @Assert\Type("string")
     * @var string
     */
    protected $country = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BreweryDTO
     */
    public function setName(string $name): BreweryDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return BreweryDTO
     */
    public function setAddress(string $address): BreweryDTO
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return BreweryDTO
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return BreweryDTO
     */
    public function setPostalCode(string $postalCode): BreweryDTO
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return BreweryDTO
     */
    public function setCountry(string $country): BreweryDTO
    {
        $this->country = $country;
        return $this;
    }
}