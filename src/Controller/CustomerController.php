<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 15.03.18
 * Time: 14:38
 */

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\PaymentHistory;
use App\Entity\Service;
use App\Service\FosUserRegister;
use App\Service\InvoiceFactory;
use App\Service\MessageGenerator;
use App\Service\PDFGenerator;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CustomerController extends Controller
{
    /**
     * @Route("/create-customer")
     */
    public function newCustomers(FosUserRegister $fosUserRegister)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $customer = new Customer();
        $customer->setIdentifier(0016);
        $customer->setName('customer16');
        $customer->setLogin('customer16');
        $customer->setEmail('customer16@mail.comm');
        $customer->setAddress('customer16 street');
        $customer->setPhoneNumber('0016');
        $customer->setAccountBalance(100);
        $customer->setPlainPassword('customer16');
        $date = new \DateTime('now');
        $customer->setServicePayedUntil($date);
        $service = new Service();
        $service->setName('nameserv');
        $service->setCost(120);
        $customer->addService($service);
        $user = $fosUserRegister->generateForUserFromType(
            Customer::CUSTOMER_TYPE,
            $customer->getName(),
            $customer->getEmail(),
            $customer->getPlainPassword()
            );
        $customer->setUser($user);
        $entityManager->persist($customer);
        $entityManager->flush();

        return new Response('Saved new customer with id ' . $customer->getId());
    }

    /**
     * @Route("/customer", name="customer")
     */
    public function customersList()
    {
        $em = $this->getDoctrine()->getManager();
        //$entityClassName = (string)ucfirst(strtolower($customerType));
        $customer = $em->getRepository('App:Service')
            ->findBy(['identifier' => '0000-0001']);
        dump(get_class($customer));die;
    }


    /**
     * @Route("/payment", name="payment")
     */
    public function newPayment(Request $request)
    {

        $req = $request->request->all();
        dump($req);
        return $this->render('customer/lol.html.twig', array());
    }

    /**
     * @Route("/send_mail/{customer_id}", name="mailing")
     * @param $name
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function indexAction($customer_id, \Swift_Mailer $mailer, InvoiceFactory $invoiceFactory, PDFGenerator $PDFGenerator, MessageGenerator $messageGenerator)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('App:Customer')
            ->findOneBy(['id' => $customer_id]);
        $services = $customer->getServices();
        $totalSum = 0;
        /** @var Service */
        foreach ($services as $service) {
            $totalSum += (integer) $service->getCost();
        }
        $invoice = $invoiceFactory->createInvoice($customer, $totalSum);
        $em->persist($invoice);
        $em->flush();

        $data = $PDFGenerator->generatePDF($customer, $services, $totalSum);
        $message = $messageGenerator->createMessage($data);
        $mailer->send($message);

        return new Response('Send succefuly', 200);
    }
//
//    /**
//     * @Route("/invoice/{customer_id}")
//     */
//    public function createInvoice($customer_id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $customer = $em->getRepository('App:Customer')
//            ->find($customer_id);
//        $services = $customer->getServices();
//        $totalSum = 0;
//                                /** @var Service */
//        foreach ($services as $service) {
//            $totalSum += (integer) $service->getCost();
//        }
//
//        $invoice = new Invoice();
//        $invoice->setCustomer($customer);
//        $invoice->setSumTotal($totalSum);
//        $em->persist($invoice);
//        $em->flush();
//        return $this->render('emails/registration.html.twig', [
//            'customer' => $customer,
//            'services' => $services,
//            'totalSum' => $totalSum
//        ]);
//    }
}