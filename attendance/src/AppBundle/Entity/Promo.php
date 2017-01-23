<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Promo
 *
 * @ORM\Table(name="promo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PromoRepository")
 */
class Promo
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
     * @var \DateTime
     *
     * @ORM\Column(name="debut_promo", type="datetime")
     */
    private $debutPromo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin_promo", type="datetime")
     */
    private $finPromo;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set debutPromo
     *
     * @param \DateTime $debutPromo
     *
     * @return Promo
     */
    public function setDebutPromo($debutPromo)
    {
        $this->debutPromo = $debutPromo;

        return $this;
    }

    /**
     * Get debutPromo
     *
     * @return \DateTime
     */
    public function getDebutPromo()
    {
        return $this->debutPromo;
    }

    /**
     * Set finPromo
     *
     * @param \DateTime $finPromo
     *
     * @return Promo
     */
    public function setFinPromo($finPromo)
    {
        $this->finPromo = $finPromo;

        return $this;
    }

    /**
     * Get finPromo
     *
     * @return \DateTime
     */
    public function getFinPromo()
    {
        return $this->finPromo;
    }
}

