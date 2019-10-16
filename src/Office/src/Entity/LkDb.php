<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LkDb
 * @ORM\Table(name="lk_db", indexes={@ORM\Index(name="id_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="\Office\Repository\OfficeRepository")
 */
class LkDb
{
    /**
     * @var int
     * @ORM\Column(
     *     name="id",
     *     type="integer",
     *     precision=0,
     *     scale=0,
     *     nullable=false,
     *     options={"unsigned"=true,"comment"="Идентификатор базы номенклатуры"},
     *     unique=false
     *     )
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(
     *     name="serial_number",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"comment"="Порядковый номер базы (не более 100 для одного пользователя)"},
     *     unique=false
     *     )
     */
    private $serialNumber;

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=180, precision=0, scale=0, nullable=true, unique=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(name="is_deleted", type="boolean", precision=0, scale=0, nullable=false, options={"default"=0}, unique=false)
     */
    private $isDeleted = 0;

    /**
     * @var \Auth\Entity\User
     * @ORM\ManyToOne(targetEntity="Auth\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="max_count", type="integer", precision=10, scale=0, nullable=false, options={"comment"="Количество"}, unique=false)
     */
    private $maxCount;

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
     * Set serialNumber.
     *
     * @param int $serialNumber
     *
     * @return LkDb
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber.
     *
     * @return int
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set name.
     *
     * @param string|null $databaseName
     *
     * @return LkDb
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return LkDb
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set user.
     *
     * @param \Auth\Entity\User|null $user
     *
     * @return LkDb
     */
    public function setUser(\Auth\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Auth\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $maxCount
     *
     * @return $this
     */
    public function setMaxCount($maxCount)
    {
        $this->maxCount = $maxCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCount()
    {
        return $this->maxCount;
    }
}
