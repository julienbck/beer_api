<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CheckinDTO
{
    /**
     * @Assert\Type("float")
     * @Assert\LessThanOrEqual("10")
     * @Assert\GreaterThanOrEqual("0")
     * @Assert\NotBlank()
     * @var float
     */
    protected $note;

    /**
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     * @var integer
     */
    protected $beerId;

    /**
     * @return float
     */
    public function getNote(): float
    {
        return $this->note;
    }

    /**
     * @param float $note
     * @return CheckinDTO
     */
    public function setNote(float $note): CheckinDTO
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return int
     */
    public function getBeerId(): ?int
    {
        return $this->beerId;
    }

    /**
     * @param int $beerId
     * @return CheckinDTO
     */
    public function setBeerId(int $beerId): CheckinDTO
    {
        $this->beerId = $beerId;
        return $this;
    }
}