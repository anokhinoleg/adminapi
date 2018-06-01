<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 05.04.18
 * Time: 15:08
 */

namespace App\Entity;

use App\Model\CustomerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity
 * @ORM\Table(name="reseller")
 * @Serializer\ExclusionPolicy("all")
 */
class Reseller implements CustomerInterface
{
    const RESELLER_TYPE = 'reseller';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_list"})
     */
    private $identifier;

    /**
     * @ORM\OneToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_list"})
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     */
    private $login;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     */
    private $email;

    /**
     * @Serializer\Expose()
     * @SWG\Property(title="password", type="string")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     */
    private $address;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     */
    private $phoneNumber;

    /**
     * @ORM\ManyToMany(targetEntity="Service", inversedBy="resellers", cascade={"persist"})
     * @ORM\JoinTable(name="reseller_services")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     * @SWG\Property(title="services", type="array", @SWG\Items(type="integer"))
     */
    private $services;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"reseller_details"})
     */
    private $accountBalance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $servicePayedUntil;

    /**
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="reseller", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     * @Serializer\Groups({"reseller_details"})
     */
    private $customers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentHistory", mappedBy="paidByReseller", cascade={"persist"})
     */
    public $paymentHistory;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->paymentHistory = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return ArrayCollection|Service
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Service $services)
    {
        $this->services = $services;
    }

    public function addService(Service $service = null)
    {
        if ($this->services->contains($service) || $service == null) {
            return;
        }

        $this->services[] = $service;
        // set the *owning* side!
        $service->setResellers();
    }

    public function removeService(Service $service)
    {
        $this->services->removeElement($service);
        // set the owning side to null
        $service->setResellers(null);
    }

    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;
    }

    public function getServicePayedUntil()
    {
        return $this->servicePayedUntil;
    }

    public function setServicePayedUntil($servicePayedUntil)
    {
        $this->servicePayedUntil = $servicePayedUntil;
    }

    /**
     * @return ArrayCollection|Customer
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    public function setCustomers(Customer $customers)
    {
        $this->customers = $customers;
    }

    public function addCustomer(Customer $customer)
    {
        if ($this->customers->contains($customer)) {
            return;
        }

        $this->customers[] = $customer;
        // set the *owning* side!
        $customer->setReseller($this);
    }

    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);
        // set the owning side to null
        $customer->setReseller(null);
    }

    /**
     * @return ArrayCollection|PaymentHistory[]
     */
    public function getPaymentHistory()
    {
        return $this->paymentHistory;
    }

    public function setPaymentHistory(PaymentHistory $paymentHistory)
    {
        $this->paymentHistory = $paymentHistory;
    }

    public function addPayment(PaymentHistory $payment = null)
    {
        if ($this->paymentHistory->contains($payment) || $payment == null) {
            return;
        }

        $this->paymentHistory[] = $payment;
        // set the *owning* side!
        $payment->setPaidByReseller($this);
    }

    public function removePayment(PaymentHistory $payment)
    {
        $this->paymentHistory->removeElement($payment);
        // set the owning side to null
        $payment->setPaidByReseller(null);
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}