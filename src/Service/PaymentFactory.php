<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 20.04.18
 * Time: 16:43
 */

namespace App\Service;

use App\Entity\Customer;
use App\Entity\PaymentHistory;
use App\Entity\Reseller;
use App\Entity\Service;
use App\Model\CustomerInterface;

class PaymentFactory
{
    public function createPayment(Service $service = null, CustomerInterface $customer, $amount, $paymentNumber)
    {
        if ($service == null) return;
        $payment = new PaymentHistory();
        $payment->setPaidService($service);
        $date = new \DateTime('now');
        $payment->setPaymentDate($date);
        if ($customer instanceof Customer) {
            $payment->setPaidByCustomer($customer);
        } elseif ($customer instanceof Reseller) {
            $payment->setPaidByReseller($customer);
        }
        $payment->setAmountPaid($amount);
        $payment->setPaymentNumber($paymentNumber);
        return $payment;
    }
}