<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefFirmVid
 * @ORM\Table(name="ref_firm_vid")
 *
 * @ORM\Entity
 */
class RefFirmVid
{
    /**
     * @var int
     * @ORM\Column(name="id", type="smallint", precision=0, scale=0, nullable=false, options={"unsigned"=true,"comment"="Идентификатор типа прошивки"}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=50, precision=0, scale=0, nullable=false, options={"comment"="Имя прошивки"}, unique=false)
     */
    private $name;


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
     * @return RefFirmVid
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
