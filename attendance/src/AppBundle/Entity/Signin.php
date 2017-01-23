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
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_users", type="integer", unique=true)
     */
    private $idUsers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="matin", type="integer")
     */
    private $matin;

    /**
     * @var int
     *
     * @ORM\Column(name="apres_midi", type="integer")
     */
    private $apresMidi;

    /**
     * Set idUsers
     *
     * @param integer $idUsers
     *
     * @return Signin
     */
    public function setIdUsers($idUsers)
    {
        $this->idUsers = $idUsers;

        return $this;
    }

    /**
     * Get idUsers
     *
     * @return int
     */
    public function getIdUsers()
    {
        return $this->idUsers;
    }

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
}

