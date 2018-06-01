<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 26.04.18
 * Time: 15:45
 */

namespace App\Entity;

use App\Model\CustomerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="invoice")
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="invoice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="string")
     */
    private $sumTotal;

    public function getId()
    {
        return $this->id;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    public function getSumTotal()
    {
        return $this->sumTotal;
    }

    public function setSumTotal($sumTotal)
    {
        $this->sumTotal = $sumTotal;
    }
}