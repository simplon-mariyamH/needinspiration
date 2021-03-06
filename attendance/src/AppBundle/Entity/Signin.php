<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Signin
 *
 * @ORM\Table(name="signature")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SigninRepository")
 */
class Signin
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    public $date;

    /**
     * @var int
     *
     * @ORM\Column(name="matin", type="integer", nullable=true)
     */
    public $matin;

    /**
     * @var int
     *
     * @ORM\Column(name="apres_midi", type="integer", nullable=true)
     */
    public $apresMidi;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Student", cascade={"persist"})
     */
    public $student;
  

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Signin
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set matin
     *
     * @param integer $matin
     *
     * @return Signin
     */
    public function setMatin($matin)
    {
        $this->matin = $matin;

        return $this;
    }

    /**
     * Get matin
     *
     * @return int
     */
    public function getMatin()
    {
        return $this->matin;
    }

    /**
     * Set apresMidi
     *
     * @param integer $apresMidi
     *
     * @return Signin
     */
    public function setApresMidi($apresMidi)
    {
        $this->apresMidi = $apresMidi;

        return $this;
    }

    /**
     * Get apresMidi
     *
     * @return int
     */
    public function getApresMidi()
    {
        return $this->apresMidi;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set student
     *
     * @param \AppBundle\Entity\Student $student
     *
     * @return Signin
     */
    public function setStudent(\AppBundle\Entity\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \AppBundle\Entity\Student
     */
    public function getStudent()
    {
        return $this->student;
    }
}
