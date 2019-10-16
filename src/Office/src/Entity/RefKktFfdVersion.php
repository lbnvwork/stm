<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefKktFfdVersion
 * @ORM\Table(name="ref_kkt_ffd_version")
 *
 * @ORM\Entity
 */
class RefKktFfdVersion
{
    /**
     * @var int
     * @ORM\Column(name="id", type="smallint", precision=0, scale=0, nullable=false, options={"unsigned"=true}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=10, precision=0, scale=0, nullable=false, unique=false)
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
     * Set versionName.
     *
     * @param string $versionName
     *
     * @return RefKktFfdVersion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get versionName.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
