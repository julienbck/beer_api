<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BeerRepository")
 *
 */
class Beer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $abv;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $ibu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brewery", inversedBy="beers")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $brewery;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Style")
     * @JMS\Expose
     * @JMS\Groups({"beer-collection", "beer-details"})
     */
    private $style;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAbv(): ?int
    {
        return $this->abv;
    }

    public function setAbv(int $abv): self
    {
        $this->abv = $abv;

        return $this;
    }

    public function getIbu(): ?int
    {
        return $this->ibu;
    }

    public function setIbu(int $ibu): self
    {
        $this->ibu = $ibu;

        return $this;
    }

    public function getBrewery(): ?Brewery
    {
        return $this->brewery;
    }

    public function setBrewery(?Brewery $brewery): self
    {
        $this->brewery = $brewery;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

        return $this;
    }
}