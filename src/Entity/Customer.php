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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Customer implements CustomerInterface
{
    const CUSTOMER_TYPE = 'customer';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_list"})
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
     * @Serializer\Groups({"customer_list"})
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     */
    private $login;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     */
    private $email;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     * @SWG\Property(title="password", type="string")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     */
    private $address;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     */
    private $phoneNumber;

    /**
     * @ORM\ManyToMany(targetEntity="Service", inversedBy="customers", cascade={"persist"})
     * @ORM\JoinTable(name="customer_services")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     * @SWG\Property(title="services", type="array", @SWG\Items(type="integer"))
     */
    private $services;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     */
    private $accountBalance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $servicePayedUntil;

    /**
     * @ORM\ManyToOne(targetEntity="Reseller", inversedBy="customers", cascade={"persist"})
     * @ORM\JoinColumn(name="reseller_id", referencedColumnName="id", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"customer_details"})
     * @SWG\Property(title="reseller_identifier", type="string")
     */
    private $reseller;

    /**
     * @ORM\OneToMany(targetEntity="PaymentHistory", mappedBy="paidByCustomer", cascade={"persist"}, orphanRemoval=true)
     */
    public $paymentHistory;

    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="customer", orphanRemoval=true)
     */
    private $invoice;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->paymentHistory = new ArrayCollection();
        $this->invoice = new ArrayCollection();
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

    public function setServices(Service $services = null)
    {
        $this->services = $services;
    }

    public function addService(Service $service = null)
    {
        if ($this->services->contains($service) || $service === null) {
            return;
        }

        $this->services[] = $service;
        // set the *owning* side!
        $service->setCustomers();
    }

    public function removeService(Service $service)
    {
        $this->services->removeElement($service);
        // set the owning side to null
        $service->setCustomers(null);
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

    public function getReseller()
    {
        return $this->reseller;
    }

    public function setReseller($reseller)
    {
        $this->reseller = $reseller;
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

    public function addPayment(PaymentHistory $payment)
    {
        if ($this->paymentHistory->contains($payment)) {
            return;
        }

        $this->paymentHistory[] = $payment;
        // set the *owning* side!
        $payment->setPaidByCustomer($this);
    }

    public function removePayment(PaymentHistory $payment)
    {
        $this->paymentHistory->removeElement($payment);
        // set the owning side to null
        $payment->setPaidByCustomer(null);
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}