<?php

namespace Office\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LkProduct
 * @ORM\Table(name="lk_product_range", indexes={@ORM\Index(name="db_id", columns={"db_id"})})
 * @ORM\Entity(repositoryClass="\Office\Repository\OfficeRepository")
 */
class LkProduct
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, options={"unsigned"=true,"comment"="Идентификатор компании"}, unique=false)
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
     *     options={"unsigned"=true,"comment"="Идентификатор товара  = (id_user * 1000000) + IDdatabase*100000 + Kode"},
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
     *     options={"unsigned"=true,"comment"="Код товара (с ноля, без пропусков)"},
     *     unique=false
     *     )
     */
    private $serialNumber;

    /**
     * @var string|null
     * @ORM\Column(name="strih", type="string", length=13, precision=0, scale=0, nullable=true, options={"comment"="Штрихкод"}, unique=false)
     */
    private $strih;

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=96, precision=0, scale=0, nullable=true, options={"comment"="Наименование товара"}, unique=false)
     */
    private $name;

    /**
     * @var float|null
     * @ORM\Column(name="count", type="float", precision=10, scale=0, nullable=true, options={"comment"="Количество"}, unique=false)
     */
    private $count;

    /**
     * @var int|null
     * @ORM\Column(name="section", type="smallint", precision=0, scale=0, nullable=true, options={"comment"="Секция"}, unique=false)
     */
    private $section;

    /**
     * @var float|null
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true, options={"comment"="Цена"}, unique=false)
     */
    private $price;

    /**
     * @var \Office\Entity\LkDb
     * @ORM\ManyToOne(targetEntity="Office\Entity\LkDb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="db_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $db;

    /**
     * @var string|null
     * @ORM\Column(name="unit_measure", type="string", length=20, precision=0, scale=0, nullable=true, options={"comment"="Единица измерения"}, unique=false)
     */
    private $unitMeasure;

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
     * Get idByFormula.
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
     * Set serialNumber.
     *
     * @param int $serialNumber
     *
     * @return LkProduct
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
     * Set strih.
     *
     * @param string|null $strih
     *
     * @return LkProduct
     */
    public function setStrih($strih = null)
    {
        $this->strih = $strih;

        return $this;
    }

    /**
     * Get strih.
     *
     * @return string|null
     */
    public function getStrih()
    {
        return $this->strih;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return LkProduct
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
     * Set count.
     *
     * @param float|null $count
     *
     * @return LkProduct
     */
    public function setCount($count = null)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count.
     *
     * @return float|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set section.
     *
     * @param int|null $section
     *
     * @return LkProduct
     */
    public function setSection($section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section.
     *
     * @return int|null
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set price.
     *
     * @param float|null $price
     *
     * @return LkProduct
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set db.
     *
     * @param \Office\Entity\LkDb|null $db
     *
     * @return LkProduct
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
     * @param null $unitMeasure
     *
     * @return $this
     */
    public function setUnitMeasure($unitMeasure = null)
    {
        $this->unitMeasure = $unitMeasure;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUnitMeasure()
    {
        return $this->unitMeasure;
    }
}
