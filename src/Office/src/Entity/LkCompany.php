<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LkCompany
 * @ORM\Table(name="lk_company")
 * @ORM\Entity(repositoryClass="\Office\Repository\OfficeRepository")
 */
class LkCompany
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, options={"unsigned"=true,"comment"="Идентификатор компании"}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=255, precision=0, scale=0, nullable=true, options={"comment"="Наименование компании"}, unique=false)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(name="inn", type="string", length=32, precision=0, scale=0, nullable=true, options={"comment"="ИНН"}, unique=false)
     */
    private $inn;

    /**
     * @var string|null
     * @ORM\Column(name="company_email", type="string", length=64, precision=0, scale=0, nullable=true, options={"comment"="email компании"}, unique=false)
     */
    private $companyEmail;

    /**
     * @var int|null
     * @ORM\Column(
     *     name="taxation_type_id",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Система налогообложения (сумма позиций ref_taxation_type->id)"},
     *     unique=false
     *     )
     */
    private $taxationTypeId;

    /**
     * @var string|null
     * @ORM\Column(name="ofd_inn", type="string", length=20, precision=0, scale=0, nullable=true, options={"comment"="ИНН ОФД"}, unique=false)
     */
    private $ofdInn;

    /**
     * @var string|null
     * @ORM\Column(name="ofd_name", type="string", length=48, precision=0, scale=0, nullable=true, options={"comment"="Наименование ОФД"}, unique=false)
     */
    private $ofdName;

    /**
     * @var bool
     * @ORM\Column(name="is_deleted", type="boolean", precision=0, scale=0, nullable=false, options={"default"=0}, unique=false)
     */
    private $isDeleted = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Auth\Entity\User", mappedBy="company", indexBy="id")
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Office\Entity\LkKkt", mappedBy="company", cascade={"remove"})
     */
    private $kkt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->kkt = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string|null $name
     *
     * @return LkCompany
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
     * Set inn.
     *
     * @param string|null $inn
     *
     * @return LkCompany
     */
    public function setInn($inn = null)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn.
     *
     * @return string|null
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * Set companyEmail.
     *
     * @param string|null $companyEmail
     *
     * @return LkCompany
     */
    public function setCompanyEmail($companyEmail = null)
    {
        $this->companyEmail = $companyEmail;

        return $this;
    }

    /**
     * Get companyEmail.
     *
     * @return string|null
     */
    public function getCompanyEmail()
    {
        return $this->companyEmail;
    }

    /**
     * Set taxationTypeId.
     *
     * @param int|null $taxationTypeId
     *
     * @return LkCompany
     */
    public function setTaxationTypeId($taxationTypeId = null)
    {
        $this->taxationTypeId = $taxationTypeId;

        return $this;
    }

    /**
     * Get taxationTypeId.
     *
     * @return int|null
     */
    public function getTaxationTypeId()
    {
        return $this->taxationTypeId;
    }

    /**
     * Set ofdInn.
     *
     * @param string|null $ofdInn
     *
     * @return LkCompany
     */
    public function setOfdInn($ofdInn = null)
    {
        $this->ofdInn = $ofdInn;

        return $this;
    }

    /**
     * Get ofdInn.
     *
     * @return string|null
     */
    public function getOfdInn()
    {
        return $this->ofdInn;
    }

    /**
     * Set ofdName.
     *
     * @param string|null $ofdName
     *
     * @return LkCompany
     */
    public function setOfdName($ofdName = null)
    {
        $this->ofdName = $ofdName;

        return $this;
    }

    /**
     * Get ofdName.
     *
     * @return string|null
     */
    public function getOfdName()
    {
        return $this->ofdName;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return LkCompany
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
     * Add user.
     *
     * @param \Auth\Entity\User $user
     *
     * @return LkCompany
     */
    public function addUser(\Auth\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user.
     *
     * @param \Auth\Entity\User $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(\Auth\Entity\User $user)
    {
        return $this->user->removeElement($user);
    }

    /**
     * Get user.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param LkKkt $kkt
     *
     * @return $this
     */
    public function addKkt(\Office\Entity\LkKkt $kkt)
    {
        $this->kkt[] = $kkt;

        return $this;
    }

    /**
     * Remove kkt.
     *
     * @param \Office\Entity\LkKkt $kkt
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeKkt(\Office\Entity\LkKkt $kkt)
    {
        return $this->kkt->removeElement($kkt);
    }

    /**
     * Get kkt.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKkt()
    {
        return $this->kkt;
    }
}
