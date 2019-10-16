<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefTaxationType
 * @ORM\Table(name="ref_taxation_type")
 *
 * @ORM\Entity
 */
class RefTaxationType
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
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
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
     * @return RefTaxationType
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
