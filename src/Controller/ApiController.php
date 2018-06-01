<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 03.04.18
 * Time: 11:26
 */

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Reseller;
use App\Entity\Service;
use App\Service\InvoiceFactory;
use App\Service\MessageGenerator;
use App\Service\PaymentFactory;
use App\Service\PaymentResponse;
use App\Service\PDFGenerator;
use App\Service\UserFactory;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends FOSRestController
{
    /**
     * @FOSRest\Post("/create-reseller")
     * @SWG\Response(
     *     response=201,
     *     description="Create Reseller and User for him",
     * )
     * @FOSRest\View(serializerGroups={"reseller_details", "reseller_list", "customer_list", "service_list"})
     * @SWG\Parameter(
     *     name="reseller",
     *     in="body",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Reseller::class),
     *     )
     * )
     */
    public function createResellerFromRequest(Request $request, UserFactory $userFactory)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $serviceIds = $data['services'];
        if (!is_array($serviceIds)) {
            $serviceIds = explode(',', $serviceIds);
        }
        $services = [];
        foreach ($serviceIds as $serviceId) {
            $service = $em->getRepository('App:Service')
                ->findOneBy(['id' => $serviceId]);
            $services[] = $service;
        }
        try {
            $reseller = $userFactory->createCustomerByType(Reseller::RESELLER_TYPE, $services);
            $em->persist($reseller);
            $em->flush();
        } catch (HttpException $exception) {
            return;
        }
        return View::create($reseller, Response::HTTP_CREATED , []);
    }

    /**
     * @FOSRest\Post("/create-customer")
     * @SWG\Response(
     *     response=201,
     *     description="Create Customer and User for him",
     * )
     * @FOSRest\View(serializerGroups={"customer_details", "customer_list", "reseller_list", "service_list"})
     * @SWG\Parameter(
     *     name="customer",
     *     in="body",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Customer::class),
     *     )
     * )
     */
    public function createCustomerFromRequest(Request $request, UserFactory $userFactory)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $serviceIds = $data['services'];
        if (!is_array($serviceIds)) {
            $serviceIds = explode(',', $serviceIds);
        }
        $services = [];
        foreach ($serviceIds as $serviceId) {
            $service = $em->getRepository('App:Service')
                ->findOneBy(['id' => $serviceId]);
            $services[] = $service;
        }
        $reseller = $em->getRepository('App:Reseller')
            ->findOneBy(['identifier' => $data['reseller']]);
        try {
            $customer = $userFactory->createCustomerByType(Customer::CUSTOMER_TYPE, $services, $reseller);
            $em->persist($customer);
            $em->flush();
        } catch (HttpException $exception) {
            return;
        }
        return View::create($customer, Response::HTTP_CREATED , []);
    }

    /**
     * @FOSRest\Post("/payment/{customerType}/{identifier}")
     * @SWG\Response(
     *     response="201",
     *     description="Create info about payment"
     * )
     * @SWG\Parameter(
     *     name="payment",
     *     in="body",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=App\Entity\PaymentHistory::class),
     *     ),
     * )
     */
    public function payment(Request $request, PaymentFactory $paymentFactory, $customerType, $identifier)
    {
        $em = $this->getDoctrine()->getManager();
        $entityClassName = (string)ucfirst(strtolower($customerType));
        $data = json_decode($request->getContent(), true);
        $customer = $em->getRepository('App:' . $entityClassName)
            ->findOneBy(['identifier' => $identifier]);
        $balance = (integer)$customer->getAccountBalance();
        $serviceIds = $data['paid_service'];
        if (!is_array($serviceIds)) {
            $serviceIds = explode(',', $serviceIds);
        }
        $payments = [];
        $paymentNumber = uniqid();
        foreach ($serviceIds as $serviceId) {
            $service = $em->getRepository('App:Service')
                ->findOneBy(['id' => $serviceId]);
            $payment = $paymentFactory
                ->createPayment(
                    $service,
                    $customer,
                    $data['amount_paid'],
                    $paymentNumber
                );
            $payments[] = $payment;
            $customer->setAccountBalance($balance + (integer)$data['amount_paid']);
            $customer->addService($service);
            $em->persist($payment);
            $em->flush();
        }
        return View::create($payments, Response::HTTP_CREATED , []);
    }

    /**
     * @FOSRest\Get("/balance/{customerType}/{identifier}")
     * @SWG\Response(
     *     response="200",
     *     description="Return customer balance"
     *     )
     */
    public function getBalance($customerType, $identifier)
    {
        $em = $this->getDoctrine()->getManager();
        $entityClassName = (string)ucfirst(strtolower($customerType));
        $customer = $em->getRepository('App:' . $entityClassName)
            ->findOneBy(['identifier' => $identifier]);
        $balance = $customer->getAccountBalance();
        return View::create($balance, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/invoice/{customerEmail}")
     * @SWG\Response(
     *     response="201",
     *     description="Create invoice"
     *     )
     */
    public function createInvoice($customerEmail, \Swift_Mailer $mailer, InvoiceFactory $invoiceFactory, PDFGenerator $generator, MessageGenerator $messageGenerator)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('App:Customer')
            ->findOneBy(['email' => $customerEmail]);
        $services = $customer->getServices();
        $totalSum = 0;
        /** @var Service */
        foreach ($services as $service) {
            $totalSum += (integer) $service->getCost();
        }
        $invoice = $invoiceFactory->createInvoice($customer, $totalSum);
        $em->persist($invoice);
        $em->flush();

        $pdf = $generator->generatePDF($customer, $services, $totalSum);

        $message = $messageGenerator->createMessageWithPDF(
            $pdf,
            'Invoice',
            'olegyurievich1991@gmail.com',
            'dchesnokov@frontdeskhelpers.com'
        );
        $mailer->send($message);

        return View::create($invoice, Response::HTTP_CREATED, []);
    }

    /**
     * @FOSRest\Put("/attach-customer/{resellerIdentifier}")
     * @SWG\Response(
     *     response="200",
     *     description="Attach customer to reseller"
     * )
     * @FOSRest\View(serializerGroups={"reseller_details", "reseller_list", "customer_list"})
     * @SWG\Parameter(
     *     name="customer_identifiers",
     *     in="body",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function attachCustomers(Request $request, $resellerIdentifier)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), false);
        $reseller = $em->getRepository('App:Reseller')
            ->findOneBy(['identifier' => $resellerIdentifier]);
        $customerIdentifiers = $data;
        if (!is_array($customerIdentifiers)) {
            $customerIdentifiers = explode(',', $customerIdentifiers);
        }
        foreach ($customerIdentifiers as $customerIdentifier) {
            $customer = $em->getRepository('App:Customer')
                ->findOneBy(['identifier' => $customerIdentifier]);
            $reseller->addCustomer($customer);
        }
        $em->persist($reseller);
        $em->flush();
        return View::create($reseller, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/attach-service/{customerType}/{identifier}")
     * @SWG\Response(
     *     response="200",
     *     description="Attach service to customer"
     * )
     * @FOSRest\View(serializerGroups={"customer_list", "customer_details", "service_list"})
     * @SWG\Parameter(
     *     name="service_identifier",
     *     in="body",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function attachService(Request $request, $customerType, $identifier)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), false);
        $entityClassName = (string)ucfirst(strtolower($customerType));
        $customer = $em->getRepository('App:' . $entityClassName)
            ->findOneBy(['identifier' => $identifier]);
        $serviceIds = $data;
        if (!is_array($serviceIds)) {
            $serviceIds = explode(',', $serviceIds);
        }
        foreach ($serviceIds as $serviceId) {
            $service = $em->getRepository('App:Service')
                ->findOneBy(['id' => $serviceId]);
            $customer->addService($service);
        }
        $em->persist($customer);
        $em->flush();
        return View::create($customer, Response::HTTP_OK);
    }
}