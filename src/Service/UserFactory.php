<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 20.04.18
 * Time: 17:02
 */

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Reseller;
use App\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserFactory
{
    private $paymentFactory;

    private $requestStack;

    private $fosUserRegister;

    public function __construct(PaymentFactory $paymentFactory, RequestStack $requestStack, FosUserRegister $fosUserRegister)
    {
        $this->paymentFactory = $paymentFactory;
        $this->requestStack = $requestStack;
        $this->fosUserRegister = $fosUserRegister;
    }

    public function createCustomerByType($type, array $services = null, CustomerInterface $customerInt = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);
        if ($type == 'customer') {
            $customer = new Customer();
            $customer->setReseller($customerInt);
        } else {
            $customer = new Reseller();
        }
        $customer->setIdentifier($data['identifier']);
        $customer->setName($data['name']);
        $customer->setLogin($data['login']);
        $customer->setEmail($data['email']);
        $customer->setAddress($data['address']);
        $customer->setPhoneNumber($data['phone_number']);
        $customer->setPlainPassword($data['plain_password']);
        $date = new \DateTime('now');
        $customer->setServicePayedUntil($date);
        $paymentNumber = uniqid();
        if (count($services) > 0) {
            foreach ($services as $service) {
                $customer->addService($service);
                $customer
                    ->addPayment(
                        $this->paymentFactory
                            ->createPayment(
                                $service,
                                $customer,
                                $data['account_balance'],
                                $paymentNumber
                            ));
            }
        }
        $user = $this->fosUserRegister->generateForUserFromType(
            $type,
            $customer->getName(),
            $customer->getEmail(),
            $customer->getPlainPassword()
        );
        $customer->setAccountBalance($data['account_balance']);
        $customer->setUser($user);
        return $customer;
    }
}