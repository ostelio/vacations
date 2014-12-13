<?php

namespace Vacation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Requests
 *
 * @ORM\Table(name="requests", indexes={@ORM\Index(name="year_idx", columns={"year"}), @ORM\Index(name="month_idx", columns={"month"}), @ORM\Index(name="user_idx", columns={"user"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Requests
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idRequest", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idrequest;

    /**
     * @var float
     *
     * @ORM\Column(name="hours", type="float", precision=10, scale=0, nullable=false)
     */
    private $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer", nullable=false)
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="day", type="integer", nullable=false)
     */
    private $day;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=45, nullable=false)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insertDate", type="datetime", nullable=false)
     */
    private $insertdate;


    /**
     * @param int $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return int
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param int $idrequest
     */
    public function setIdrequest($idrequest)
    {
        $this->idrequest = $idrequest;
    }

    /**
     * @return int
     */
    public function getIdrequest()
    {
        return $this->idrequest;
    }

    /**
     * @param \DateTime $insertdate
     */
    public function setInsertdate($insertdate)
    {
        $this->insertdate = $insertdate;
    }

    /**
     * @return \DateTime
     */
    public function getInsertdate()
    {
        return $this->insertdate;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     *  @ORM\PrePersist
     */
    public function timestamp()
    {
        if(is_null($this->getInsertdate())) {
            $this->setInsertdate(new \DateTime());
        }
        return $this;
    }
}
