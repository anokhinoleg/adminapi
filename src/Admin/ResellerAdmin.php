<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 05.04.18
 * Time: 16:09
 */

namespace App\Admin;

use App\Entity\Customer;
use App\Entity\Service;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ResellerAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('identifier', TextType::class)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
        ;
        if ($this->isCurrentRoute('create')) {
            $form
                ->add('login', TextType::class)
                ->add('plainPassword', TextType::class, [
                'mapped' => false,
                'required' => true]);
        }
        $form
            ->add('address')
            ->add('phoneNumber')
            ->add('accountBalance')
            ->add('servicePayedUntil', DateTimePickerType::class, [
                'format'=> 'dd/MM/yyyy hh:mm',
                'dp_side_by_side' => true,
            ])
            ->add('services', ModelType::class, [
                'class' => Service::class,
                'multiple' => true,
                'property' => 'name',
                'btn_add' => 'Add'
            ])
            ->add('customers',EntityType::class, [
                'class' => Customer::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
        ;

    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('login')
            ->add('address')
            ->add('phoneNumber')
            ->add('accountBalance')
            ->add('services', CollectionType::class)
            ->add('customers', CollectionType::class)
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $paymentsHistory = [];
        $payments = $this->getSubject()->getPaymentHistory();
        foreach ($payments as $payment) {
            $paymentNumber = $payment->getPaymentNumber();
            $paymentsHistory[$paymentNumber][] = $payment;
        }
        $this->paymentHistory = $paymentsHistory;
        $showMapper
            ->tab('General') // the tab call is optional
            ->with('Profile', [
                'class'       => 'col-md-8',
                'box_class'   => 'box box-solid box-info',
                'description' => 'Lorem ipsum',
            ])
            ->add('name')
            ->add('login')
            ->add('address')
            ->add('phoneNumber')
            ->add('accountBalance')
            ->add('customers')
            ->add('services')
            ->end()
            ->end()
            ->tab('Payment History');

        $showMapper
            ->with('Payments', [
                'class'       => 'col-md-8',
                'box_class'   => 'box box-solid box-danger',
            ])
            ->add('paymentHistory',CollectionType::class, [
                'label' => ' ',
                'template' => '@App/Admin/payments.html.twig'
            ])
            ->end();
        $showMapper
            ->end()
        ;
    }
}