<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 20.04.18
 * Time: 17:39
 */

namespace App\Model;

use App\Entity\PaymentHistory;
use App\Entity\Service;

interface CustomerInterface
{
    public function getId();

    public function getName();

    public function setName($name);

    public function getLogin();

    public function setLogin($login);

    public function getEmail();

    public function setEmail($email);

    public function getPlainPassword();

    public function setPlainPassword($plainPassword);

    public function getAddress();

    public function setAddress($address);

    public function getPhoneNumber();

    public function setPhoneNumber($phoneNumber);

    public function getServices();

    public function setServices(Service $services);

    public function addService(Service $service);

    public function removeService(Service $service);

    public function getAccountBalance();

    public function setAccountBalance($accountBalance);

    public function getServicePayedUntil();

    public function setServicePayedUntil($servicePayedUntil);

    public function getPaymentHistory();

    public function setPaymentHistory(PaymentHistory $paymentHistory);

    public function addPayment(PaymentHistory $payment);

    public function removePayment(PaymentHistory $payment);
}