<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 16.03.18
 * Time: 17:58
 */

namespace App\Admin;

use App\Entity\Reseller;
use App\Entity\Service;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerAdmin extends AbstractAdmin
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
                'dp_side_by_side'       => true,
            ])
            ->add('services', ModelType::class, [
                'class' => Service::class,
                'multiple' => true,
                'property' => 'name',
                'btn_add' => 'Add'
            ])
            ->add('reseller', EntityType::class, [
                'class' => Reseller::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Choose reseller',
                'data_class' => null,
                'multiple' => false
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
            ->add('reseller')
            ->add('_action', null, [
                'actions' => [
                    'show' => [

                    ],
                    'edit' => [
                        'template' => '@App/Admin/list_action_edit.html.twig',
                    ],
                    'delete' => [
                        'template' => '@App/Admin/list_action_delete.html.twig',
                    ],
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

    public function configureShowFields(ShowMapper $showMapper)
    {
        $paymentsHistory = [];
        $payments = $this->getSubject()->getPaymentHistory();
        foreach ($payments as $payment) {
            $paymentNumber = $payment->getPaymentNumber();
            $paymentsHistory[$paymentNumber][] = $payment;
        }
        dump($paymentsHistory);
        $this->paymentHistory = $paymentsHistory;
        $showMapper
            ->tab('General') // the tab call is optional
            ->with('Profile', [
                'class'       => 'col-md-8',
                'box_class'   => 'box box-solid box-info',
                'description' => 'Lorem ipsum',
            ])
            ->add('name', null, [
                'label' => 'Name: '
            ])
            ->add('login', null, [
                'label' => 'Login: '
            ])
            ->add('address', null, [
                'label' => 'Address: '
            ])
            ->add('phoneNumber', null, [
                'label' => 'Phone Number: '
            ])
            ->add('accountBalance', null, [
                'label' => 'Balance: '
            ])
            ->add('services', EntityType::class, [
                'label' => 'Services: ',
            ])
            ->add('reseller', null, [
                'label' => 'Reseller : '
            ])
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