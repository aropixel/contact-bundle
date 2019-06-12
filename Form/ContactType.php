<?php

namespace Aropixel\ContactBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('read', ChoiceType::class, array(
                'choices'  => array(
                    'Oui' => '1',
                    'Non' => '0',
                ),
                'empty_data' => 'Non',
                'label' => 'Lu',
                'expanded' => true
            ))
            ->add('answered', ChoiceType::class, array(
                'choices'  => array(
                    'Oui' => '1',
                    'Non' => '0',
                ),
                'empty_data' => 'Non',
                'label' => 'Répondu',
                'expanded' => true
            ))
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
