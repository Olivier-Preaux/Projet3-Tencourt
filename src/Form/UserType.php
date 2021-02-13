<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
            ])
/*          ->add('password') */ //We have to know how to re encrypte the password through this form
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('sex', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                ]
            ])
            ->add('level', ChoiceType::class, [
                'label' => 'Niveau',
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermediaire' => 'Intermediaire',
                    'Expert' => 'Expert',
                    'Professionnel' => 'Professionnel',
                ]
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'label' => 'Adresse postale',
            ])
            ->add('postalcode', TextType::class, [
                'required' => false,
                'label' => 'Code postale',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Telephone',
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date d\'anniversaire',
                'format' => 'dd-MM-yyyy',
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'years' => range(2003, 1930),
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('update', SubmitType::class, [
                'label' => 'Sauvegarder',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
