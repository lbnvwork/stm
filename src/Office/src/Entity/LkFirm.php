<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LkFirm
 * @ORM\Table(name="lk_firm", indexes={@ORM\Index(name="vid_id", columns={"vid_id"}), @ORM\Index(name="id_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="\Office\Repository\OfficeRepository")
 */
class LkFirm
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, options={"unsigned"=true,"comment"="Идентификатор прошивки"}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(
     *     name="id_by_formula",
     *     type="bigint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Идентификатор прошивки = (id_user * 100) + id_user_firm"},
     *     unique=false
     *     )
     */
    private $idByFormula;

    /**
     * @var int
     * @ORM\Column(
     *     name="serial_number",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=false,
     *     options={"unsigned"=true,"comment"="Порядковый номер прошивки для пользователя (целые с ноля, без пропусков)"},
     *     unique=false
     *     )
     */
    private $serialNumber;

    /**
     * @var string|null
     * @ORM\Column(name="inf", type="string", length=160, precision=0, scale=0, nullable=true, options={"comment"="Дополнительная информация"}, unique=false)
     */
    private $inf;

    /**
     * @var string|null
     * @ORM\Column(name="filename", type="string", length=180, precision=0, scale=0, nullable=true, options={"comment"="Имя файла"}, unique=false)
     */
    private $filename;

    /**
     * @var string|null
     * @ORM\Column(name="image_type", type="string", length=25, precision=0, scale=0, nullable=true, options={"comment"="Тип файла (расширение)"}, unique=false)
     */
    private $imageType;

    /**
     * @var int|null
     * @ORM\Column(name="size", type="bigint", precision=0, scale=0, nullable=true, options={"unsigned"=true,"comment"="Размер файла"}, unique=false)
     */
    private $size;

    /**
     * @var string|null
     * @ORM\Column(name="file", type="blob", length=16777215, precision=0, scale=0, nullable=true, options={"comment"="Файл"}, unique=false)
     */
    private $file;

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
     * @var \Office\Entity\RefFirmVid
     * @ORM\ManyToOne(targetEntity="Office\Entity\RefFirmVid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vid_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $vid;

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
     * Get firmIdByFormula.
     *
     * @return int
     */
    public function getIdByFormula()
    {
        return $this->idByFormula;
    }

    /**
     * Set idByFormula.
     *
     * @param $idByFormula
     *
     * @return $this
     */
    public function setIdByFormula($idByFormula)
    {
        $this->idByFormula = $idByFormula;

        return $this;
    }

    /**
     * Set SerialNumber.
     *
     * @param int $serialNumber
     *
     * @return LkFirm
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set inf.
     *
     * @param string|null $inf
     *
     * @return LkFirm
     */
    public function setInf($inf = null)
    {
        $this->inf = $inf;

        return $this;
    }

    /**
     * Get inf.
     *
     * @return string|null
     */
    public function getInf()
    {
        return $this->inf;
    }

    /**
     * Set filename.
     *
     * @param string|null $filename
     *
     * @return LkFirm
     */
    public function setFilename($filename = null)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set imageType.
     *
     * @param string|null $imageType
     *
     * @return LkFirm
     */
    public function setImageType($imageType = null)
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType.
     *
     * @return string|null
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Set size.
     *
     * @param int|null $size
     *
     * @return LkFirm
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set file.
     *
     * @param string|null $file
     *
     * @return LkFirm
     */
    public function setFile($file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return LkFirm
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
     * @return LkFirm
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
     * Set vid.
     *
     * @param \Office\Entity\RefFirmVid|null $vid
     *
     * @return LkFirm
     */
    public function setVid(\Office\Entity\RefFirmVid $vid = null)
    {
        $this->vid = $vid;

        return $this;
    }

    /**
     * Get vid.
     *
     * @return \Office\Entity\RefFirmVid|null
     */
    public function getVid()
    {
        return $this->vid;
    }
}
