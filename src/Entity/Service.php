<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 22.03.18
 * Time: 13:27
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="service")
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Service
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups({"service_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     * @Serializer\Groups({"service_list"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Customer", mappedBy="services", cascade={"persist"}))
     */
    private $customers;

    /**
     * @ORM\ManyToMany(targetEntity="Reseller", mappedBy="services", cascade={"persist"})
     */
    private $resellers;

    /**
     * @ORM\Column(type="string")
     */
    private $cost;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentHistory", mappedBy="paidService", orphanRemoval=true)
     */
    private $payment;

    public function __construct()
    {
        $this->payment = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCustomers()
    {
        return $this->customers;
    }

    public function setCustomers(Customer $customer = null)
    {
        $this->customers = $customer;
    }

    public function getResellers()
    {
        return $this->resellers;
    }

    public function setResellers(Reseller $resellers = null)
    {
        $this->resellers = $resellers;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}