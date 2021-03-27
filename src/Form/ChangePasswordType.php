<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('old_password', PasswordType::class, [
            'label' => 'Mot de passe actuel',
            'required' => true,
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Veuillez entrer votre mot de passe actuel'
            ]
        ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confimation doivent être identique',
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => true,
                'first_options' => [
                    'label' => ' Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez votre nouveau mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre mot de passe'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
