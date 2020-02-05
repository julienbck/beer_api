<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     *
     * @Groups({"beer-collection", "beer-details", "user-details-public"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"beer-collection", "beer-details", "user-details-public"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $abv;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $ibu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brewery", inversedBy="beers")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $brewery;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Style")
     *
     * @Groups({"beer-collection", "beer-details"})
     */
    private $style;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Checkin", mappedBy="beer", orphanRemoval=true)
     */
    private $checkins;

    public function __construct()
    {
        $this->checkins = new ArrayCollection();
    }

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

    /**
     * @return Collection|Checkin[]
     */
    public function getCheckins(): Collection
    {
        return $this->checkins;
    }

    public function addCheckin(Checkin $checkin): self
    {
        if (!$this->checkins->contains($checkin)) {
            $this->checkins[] = $checkin;
            $checkin->setBeer($this);
        }

        return $this;
    }

    public function removeCheckin(Checkin $checkin): self
    {
        if ($this->checkins->contains($checkin)) {
            $this->checkins->removeElement($checkin);
            // set the owning side to null (unless already changed)
            if ($checkin->getBeer() === $this) {
                $checkin->setBeer(null);
            }
        }

        return $this;
    }

}
