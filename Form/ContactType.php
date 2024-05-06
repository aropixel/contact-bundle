<?php

namespace Aropixel\ContactBundle\Form;

use Aropixel\AdminBundle\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('read', ToggleSwitchType::class, [
                'empty_data' => 'Non',
                'label' => 'Lu',
            ])
            ->add('answered', ToggleSwitchType::class, [
                'empty_data' => 'Non',
                'label' => 'Répondu',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'aropixelcontact_contact';
    }

}
