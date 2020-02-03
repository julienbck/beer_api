<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CheckinDTO
{
    /**
     * @Assert\Type("integer")
     * @Assert\LessThanOrEqual("10")
     * @Assert\GreaterThanOrEqual("0")
     * @Assert\NotBlank()
     * @var integer
     */
    protected $note;

    /**
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     * @var integer
     */
    protected $beerId;

    /**
     * @return int
     */
    public function getNote(): int
    {
        return $this->note;
    }

    /**
     * @param int $note
     * @return CheckinDTO
     */
    public function setNote(int $note): CheckinDTO
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return int
     */
    public function getBeerId(): int
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