<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 10.04.18
 * Time: 17:48
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_history")
 */
class PaymentHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Exclude()
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Exclude()
     */
    private $paymentDate;

    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="payment")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * @SWG\Property(title="services", type="array", @SWG\Items(type="integer"))
     */
    private $paidService;

    /**
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(type="float")
     */
    private $amountPaid;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="paymentHistory")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * @Serializer\Exclude()
     */
    private $paidByCustomer;

    /**
     * @ORM\ManyToOne(targetEntity="Reseller", inversedBy="paymentHistory")
     * @ORM\JoinColumn(name="reseller_id", referencedColumnName="id")
     * @Serializer\Exclude()
     */
    private $paidByReseller;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     */
    private $paymentNumber;

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;
    }

    public function getPaidService()
    {
        return $this->paidService;
    }

    public function setPaidService(Service $paidService)
    {
        $this->paidService = $paidService;
    }

    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    public function setAmountPaid($amountPaid)
    {
        $this->amountPaid = $amountPaid;
    }

    public function getPaidByCustomer()
    {
        return $this->paidByCustomer;
    }

    public function setPaidByCustomer(Customer $customer)
    {
        $this->paidByCustomer = $customer;
    }

    public function getPaidByReseller()
    {
        return $this->paidByReseller;
    }

    public function setPaidByReseller(Reseller $paidByReseller)
    {
        $this->paidByReseller = $paidByReseller;
    }

    public function getPaymentNumber()
    {
        return $this->paymentNumber;
    }

    public function setPaymentNumber($paymentNumber)
    {
        $this->paymentNumber = $paymentNumber;
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}