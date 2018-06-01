<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 11.05.18
 * Time: 16:50
 */

namespace App\Service;


use App\Entity\Invoice;
use App\Model\CustomerInterface;

class InvoiceFactory
{
    public function createInvoice(CustomerInterface $customer, $totalSum)
    {
        $invoice = new Invoice();
        $invoice->setCustomer($customer);
        $invoice->setSumTotal($totalSum);
        return $invoice;
    }
}