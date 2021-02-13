<?php

namespace App\Form;

use App\Service\SearchService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SearchUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('level', ChoiceType::class, [
                'label' => 'Niveau',
                'required' => false,
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermediaire' => 'Intermediaire',
                    'Expert' => 'Expert',
                    'Professionnel' => 'Professionnel',
                ]
            ])
            ->add('sex', ChoiceType::class, [
                'label' => 'Sexe',
                'required' => false,
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                ]
            ])
            ->add('min', IntegerType::class, [
                'label' => 'Age minimum',
                'required' => false,
                'attr' => [
                    'min' => 18,
                    'max' => 100,
                ]
            ])
            ->add('max', IntegerType::class, [
                'label' => 'Age maximum',
                'required' => false,
                'attr' => [
                    'min' => 18,
                    'max' => 100,
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Adresse / Ville',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchService::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
