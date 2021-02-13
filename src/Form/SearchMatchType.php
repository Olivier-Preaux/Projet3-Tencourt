<?php

namespace App\Form;

use App\Entity\TennisMatch;
use App\Service\SearchMatchService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SearchMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adress', TextType::class, [
                'label' => 'Adresse / Ville',
                'required' => false,
            ])
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
            ->add('min', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('max', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('startHour', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endHour', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchMatchService::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
