<?php

namespace Auth\Entity;

use App\Service\DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Office\Entity\LkDb;
use Office\Entity\LkFirm;
use Permission\Entity\Role;
use Zend\Expressive\Authentication\UserInterface;

/**
 * User
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="\Cms\Repository\CmsRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=32, precision=0, scale=0, nullable=false, unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=60, precision=0, scale=0, nullable=true, unique=false)
     */
    private $password;

    /**
     * @var string|null
     * @ORM\Column(name="first_name", type="string", length=32, precision=0, scale=0, nullable=true, unique=false)
     */
    private $firstName;

    /**
     * @var string|null
     * @ORM\Column(name="middle_name", type="string", length=32, precision=0, scale=0, nullable=true, unique=false)
     */
    private $middleName;

    /**
     * @var string|null
     * @ORM\Column(name="last_name", type="string", length=32, precision=0, scale=0, nullable=true, unique=false)
     */
    private $lastName;

    /**
     * @var string|null
     * @ORM\Column(name="hash_key", type="string", length=32, precision=0, scale=0, nullable=true, unique=false)
     */
    private $hashKey;

    /**
     * @var string|null
     * @ORM\Column(name="phone", type="string", length=34, precision=0, scale=0, nullable=true, unique=false)
     */
    private $phone;

    /**
     * @var bool
     * @ORM\Column(name="is_confirmed", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $isConfirmed = 0;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_create", type="datetime", precision=0, scale=0, nullable=false, options={"default"="CURRENT_TIMESTAMP"}, unique=false)
     */
    private $dateCreate = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     * @ORM\Column(name="date_last_auth", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $dateLastAuth;

    /**
     * @var UserHasRole
     * @ORM\OneToMany(targetEntity="Auth\Entity\UserHasRole", mappedBy="user", cascade={"ALL"}, indexBy="roleName")
     */
    private $userRole;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Office\Entity\LkCompany", inversedBy="user")
     * @ORM\JoinTable(name="user_has_company",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     *   }
     * )
     */
    private $company;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Office\Entity\LkDb", mappedBy="user", cascade={"remove"})
     */
    private $db;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Office\Entity\LkFirm", mappedBy="user", cascade={"remove"})
     */
    private $firm;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Auth\Entity\User", inversedBy="user")
     * @ORM\JoinTable(name="user_has_customer",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     *   }
     * )
     */
    private $customer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->company = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userRole = new \Doctrine\Common\Collections\ArrayCollection();
        $this->db = new \Doctrine\Common\Collections\ArrayCollection();
        $this->firm = new \Doctrine\Common\Collections\ArrayCollection();
        $this->customer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setDateCreate(new DateTime());
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
     * Set Id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set middleName.
     *
     * @param string $middleName
     *
     * @return User
     */
    public function setMiddleName(string $middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName.
     *
     * @return string
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set hashKey.
     *
     * @param string|null $hashKey
     *
     * @return User
     */
    public function setHashKey(?string $hashKey)
    {
        $this->hashKey = $hashKey;

        return $this;
    }

    /**
     * Get hashKey.
     *
     * @return string|null
     */
    public function getHashKey(): ?string
    {
        return $this->hashKey;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return User
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isConfirmed.
     *
     * @param bool $isConfirmed
     *
     * @return User
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get isConfirmed.
     *
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime $dateCreate
     *
     * @return User
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate.
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateLastAuth.
     *
     * @param \DateTime|null $dateLastAuth
     *
     * @return User
     */
    public function setDateLastAuth($dateLastAuth = null)
    {
        $this->dateLastAuth = $dateLastAuth;

        return $this;
    }

    /**
     * Get dateLastAuth.
     *
     * @return \DateTime|null
     */
    public function getDateLastAuth(): ?\DateTime
    {
        return $this->dateLastAuth;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setNewPassword(string $password): User
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    /**
     * @return array
     */
    public function getRbacRoles(): array
    {
        $roles = $this->getUserRoles();
        $rbacRoles = [];

        foreach ($roles as $role) {
            $rbacRoles[] = new \Zend\Permissions\Rbac\Role($role->getRoleName());
        }

        return $rbacRoles;
    }

    /**
     * @return string|null
     */
    public function getUsername(): string
    {
        return $this->getEmail().'';
    }

    /**
     * @return Role[]
     */
    public function getUserRoles(): array
    {
        return $this->userRole->toArray();
    }

    /**
     * @return PersistentCollection
     */
    public function getUserRoleManager()
    {
        return $this->userRole;
    }

    public function getFIO(): string
    {
        return $this->getLastName().' '.$this->getFirstName().' '.$this->getMiddleName();
    }

    public function getIdentity(): string
    {
        return $this->getId();
    }

    public function getRoles(): iterable
    {
        return $this->userRole->toArray();
    }

    public function getDetail(string $name, $default = null)
    {
        // TODO: Implement getDetail() method.
    }

    public function getDetails(): array
    {
        // TODO: Implement getDetails() method.
        return [];
    }

    /**
     * Add company.
     *
     * @param \Office\Entity\LkCompany $company
     *
     * @return User
     */
    public function addCompany(\Office\Entity\LkCompany $company)
    {
        $this->company[] = $company;

        return $this;
    }

    /**
     * Add company.
     *
     * @param \Office\Entity\LkCompany $company
     *
     * @return User
     */
    public function setCompany(\Office\Entity\LkCompany $company)
    {
        $this->company[] = $company;

        return $this;
    }

    /**
     * Remove company.
     *
     * @param \Office\Entity\LkCompany $company
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCompany(\Office\Entity\LkCompany $company)
    {
        return $this->company->removeElement($company);
    }

    /**
     * Get company.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param LkDb $db
     *
     * @return $this
     */
    public function addKkt(\Office\Entity\LkDb $db)
    {
        $this->db[] = $db;

        return $this;
    }

    /**
     * Remove kkt.
     *
     * @param \Office\Entity\LkKkt $kkt
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeKkt(\Office\Entity\LkDb $db)
    {
        return $this->db->removeElement($db);
    }

    /**
     * Get kkt.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param LkFirm $firm
     *
     * @return $this
     */
    public function addFirm(\Office\Entity\LkFirm $firm)
    {
        $this->db[] = $firm;

        return $this;
    }

    /**
     * Remove firm.
     *
     * @param \Office\Entity\LkFirm $firm
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFirm(\Office\Entity\LkFirm $firm)
    {
        return $this->db->removeElement($firm);
    }

    /**
     * Get firm.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFirm()
    {
        return $this->firm;
    }

    /**
     * Add user.
     *
     * @param \Auth\Entity\User $user
     *
     * @return User
     */
    public function setCtoUser(\Auth\Entity\User $user)
    {
        $this->customer[] = $user;

        return $this;
    }

    /**
     * Remove user.
     *
     * @param \Auth\Entity\User $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCtoUser(\Auth\Entity\User $user)
    {
        return $this->customer->removeElement($user);
    }

    /**
     * Get user.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
