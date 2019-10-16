<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LkKkt
 *
 * @ORM\Table(
 *     name="lk_kkt",
 *     indexes={
 *          @ORM\Index(name="firm_id", columns={"firm_id"}),
 *          @ORM\Index(name="db_id", columns={"db_id"}),
 *          @ORM\Index(name="ffd_kkt_version_id", columns={"ffd_kkt_version"}),
 *          @ORM\Index(name="company_id", columns={"company_id"})}
 * )
 * @ORM\Entity(repositoryClass="\Office\Repository\OfficeRepository")
 */
class LkKkt
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, options={"unsigned"=true,"comment"="Идентификатор кассы"}, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(
     *     name="id_by_formula",
     *     type="bigint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Идентификатор кассы   = user_id * 1000000 + kkt_id"},
     *     unique=false
     * )
     */
    private $idByFormula;

    /**
     * @var int
     *
     * @ORM\Column(
     *     name="serial_number",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=false,
     *     options={"unsigned"=true,"comment"="Порядковый номер кассы для пользователя  (целые с ноля, без пропусков)"},
     *     unique=false
     * )
     */
    private $serialNumber;

    /**
     * @var string|null
     * @ORM\Column(name="retail_place", type="string", length=96, precision=0, scale=0, nullable=true, options={"comment"="Место раcчетов"}, unique=false)
     */
    private $retailPlace;

    /**
     * @var string|null
     * @ORM\Column(name="kkt_reg_id", type="string", length=20, precision=0, scale=0, nullable=true, options={"comment"="Регистрационный номер ККТ"}, unique=false)
     */
    private $kktRegId;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="datetime", type="datetime", precision=0, scale=0, nullable=true, options={"comment"="Дата установки кассы по чеку"}, unique=false)
     */
    private $datetime;

    /**
     * @var int|null
     * @ORM\Column(
     *     name="kkt_mode",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Режим работы (сумма позиций ref_kkt_mode->id) "},
     *     unique=false
     *     )
     */
    private $kktMode;

    /**
     * @var int|null
     * @ORM\Column(
     *     name="kkt_advanced_mode",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Расширенный режим работы (сумма позиций ref_kkt_advanced_mode->id) "},
     *     unique=false
     *     )
     */
    private $kktAdvancedMode;

    /**
     * @var int|null
     * @ORM\Column(
     *     name="payment_agent_type",
     *     type="smallint",
     *     precision=0,
     *     scale=0,
     *     nullable=true,
     *     options={"unsigned"=true,"comment"="Признак агента (сумма позиций ref_payment_agent_type->id) "},
     *     unique=false
     *     )
     */
    private $paymentAgentType;

    /**
     * @var string|null
     * @ORM\Column(name="fps", type="string", length=6, precision=0, scale=0, nullable=true, options={"comment"="Фискальный признак из чека"}, unique=false)
     */
    private $fps;

    /**
     * @var bool|null
     * @ORM\Column(name="synchronization", type="boolean", precision=0, scale=0, nullable=true, options={"comment"="Синхронизация"}, unique=false)
     */
    private $synchronization;

    /**
     * @var int
     *
     * @ORM\Column(name="seria", type="smallint", nullable=false, options={"unsigned"=true,"comment"="Серия ККТ"})
     */
    private $seria;

    /**
     * @var string
     *
     * @ORM\Column(name="machine_number", type="string", length=7, nullable=false, options={"comment"="Заводской номер ККТ"})
     */
    private $machineNumber;

    /**
     * @var string
     * @ORM\Column(name="retail_address", type="string", length=254, precision=0, scale=0, nullable=false, options={"comment"="Адрес рассчетов"}, unique=false)
     */
    private $retailAddress;

    /**
     * @var bool
     * @ORM\Column(name="is_deleted", type="boolean", precision=0, scale=0, nullable=false, options={"default"=0}, unique=false)
     */
    private $isDeleted = 0;

    /**
     * @var \Office\Entity\LkDb
     * @ORM\ManyToOne(targetEntity="Office\Entity\LkDb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="db_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $db;

    /**
     * @var \Office\Entity\LkFirm
     * @ORM\ManyToOne(targetEntity="Office\Entity\LkFirm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="firm_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $firm;

    /**
     * @var \Office\Entity\LkCompany
     * @ORM\ManyToOne(targetEntity="Office\Entity\LkCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $company;

    /**
     * @var \Office\Entity\RefKktFfdVersion
     * @ORM\ManyToOne(targetEntity="Office\Entity\RefKktFfdVersion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ffd_kkt_version", referencedColumnName="id", nullable=true)
     * })
     */
    private $ffdKktVersion;

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
     * Set idByFormula.
     *
     * @param int $idByFormula
     *
     * @return LkKkt
     */
    public function setIdByFormula($idByFormula)
    {
        $this->idByFormula = $idByFormula;

        return $this;
    }

    /**
     * Get idByFormula.
     *
     * @return int
     */
    public function getIdByFormula()
    {
        return $this->idByFormula;
    }

    /**
     * Set serialNumber.
     *
     * @param int $serialNumber
     *
     * @return LkKkt
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
     * Set retailPlace.
     *
     * @param string|null $retailPlace
     *
     * @return LkKkt
     */
    public function setRetailPlace($retailPlace = null)
    {
        $this->retailPlace = $retailPlace;

        return $this;
    }

    /**
     * Get retailPlace.
     *
     * @return string|null
     */
    public function getRetailPlace()
    {
        return $this->retailPlace;
    }

    /**
     * Set kktRegId.
     *
     * @param string|null $kktRegId
     *
     * @return LkKkt
     */
    public function setKktRegId($kktRegId = null)
    {
        $this->kktRegId = $kktRegId;

        return $this;
    }

    /**
     * Get kktRegId.
     *
     * @return string|null
     */
    public function getKktRegId()
    {
        return $this->kktRegId;
    }

    /**
     * Set datetime.
     *
     * @param \DateTime|null $datetime
     *
     * @return LkKkt
     */
    public function setDatetime($datetime = null)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime.
     *
     * @return \DateTime|null
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set kktMode.
     *
     * @param int|null $kktMode
     *
     * @return LkKkt
     */
    public function setKktMode($kktMode = null)
    {
        $this->kktMode = $kktMode;

        return $this;
    }

    /**
     * Get kktMode.
     *
     * @return int|null
     */
    public function getKktMode()
    {
        return $this->kktMode;
    }

    /**
     * Set kktAdvancedMode.
     *
     * @param int|null $kktAdvancedMode
     *
     * @return LkKkt
     */
    public function setKktAdvancedMode($kktAdvancedMode = null)
    {
        $this->kktAdvancedMode = $kktAdvancedMode;

        return $this;
    }

    /**
     * Get kktAdvancedMode.
     *
     * @return int|null
     */
    public function getKktAdvancedMode()
    {
        return $this->kktAdvancedMode;
    }

    /**
     * Set paymentAgentType.
     *
     * @param int|null $paymentAgentType
     *
     * @return LkKkt
     */
    public function setPaymentAgentType($paymentAgentType = null)
    {
        $this->paymentAgentType = $paymentAgentType;

        return $this;
    }

    /**
     * Get paymentAgentType.
     *
     * @return int|null
     */
    public function getPaymentAgentType()
    {
        return $this->paymentAgentType;
    }

    /**
     * Set fps.
     *
     * @param string|null $fps
     *
     * @return LkKkt
     */
    public function setFps($fps = null)
    {
        $this->fps = $fps;

        return $this;
    }

    /**
     * Get fps.
     *
     * @return string|null
     */
    public function getFps()
    {
        return $this->fps;
    }

    /**
     * Set synchronization.
     *
     * @param bool|null $synchronization
     *
     * @return LkKkt
     */
    public function setSynchronization($synchronization = null)
    {
        $this->synchronization = $synchronization;

        return $this;
    }

    /**
     * Get synchronization.
     *
     * @return bool|null
     */
    public function getSynchronization()
    {
        return $this->synchronization;
    }

    /**
     * Set seria.
     *
     * @param int $seria
     *
     * @return LkKkt
     */
    public function setSeria($seria)
    {
        $this->seria = $seria;

        return $this;
    }

    /**
     * Get seria.
     *
     * @return int
     */
    public function getSeria()
    {
        return $this->seria;
    }

    /**
     * Set machineNumber.
     *
     * @param string $machineNumber
     *
     * @return LkKkt
     */
    public function setMachineNumber($machineNumber)
    {
        $this->machineNumber = $machineNumber;

        return $this;
    }

    /**
     * Get machineNumber.
     *
     * @return string
     */
    public function getMachineNumber()
    {
        return $this->machineNumber;
    }

    /**
     * Set retailAddress.
     *
     * @param string $retailAddress
     *
     * @return LkKkt
     */
    public function setRetailAddress($retailAddress)
    {
        $this->retailAddress = $retailAddress;

        return $this;
    }

    /**
     * Get retailAddress.
     *
     * @return string
     */
    public function getRetailAddress()
    {
        return $this->retailAddress;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return LkKkt
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
     * Set db.
     *
     * @param \Office\Entity\LkDb|null $db
     *
     * @return LkKkt
     */
    public function setDb(\Office\Entity\LkDb $db = null)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Get db.
     *
     * @return \Office\Entity\LkDb|null
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set firm.
     *
     * @param \Office\Entity\LkFirm|null $firm
     *
     * @return LkKkt
     */
    public function setFirm(\Office\Entity\LkFirm $firm = null)
    {
        $this->firm = $firm;

        return $this;
    }

    /**
     * Get firm.
     *
     * @return \Office\Entity\LkFirm|null
     */
    public function getFirm()
    {
        return $this->firm;
    }

    /**
     * Set company.
     *
     * @param \Office\Entity\LkCompany|null $company
     *
     * @return LkKkt
     */
    public function setCompany(\Office\Entity\LkCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company.
     *
     * @return \Office\Entity\LkCompany|null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set ffdKktVersion.
     *
     * @param \Office\Entity\RefKktFfdVersion|null $ffdKktVersion
     *
     * @return LkKkt
     */
    public function setFfdKktVersion(\Office\Entity\RefKktFfdVersion $ffdKktVersion = null)
    {
        $this->ffdKktVersion = $ffdKktVersion;

        return $this;
    }

    /**
     * Get ffdKktVersion.
     *
     * @return \Office\Entity\RefKktFfdVersion|null
     */
    public function getFfdKktVersion()
    {
        return $this->ffdKktVersion;
    }
}
