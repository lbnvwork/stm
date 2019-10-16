<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefKktMode
 * @ORM\Table(name="ref_kkt_mode")
 *
 * @ORM\Entity
 */
class RefKktMode
{
    /**
     * @var int
     * @ORM\Column(name="id", type="smallint", precision=0, scale=0, nullable=false, options={"unsigned"=true}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=30, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return RefKktMode
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return RefKktMode
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
