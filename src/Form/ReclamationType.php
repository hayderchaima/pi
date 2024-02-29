<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Categorie;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname')
            ->add('categories',EntityType::class,  [
                'class' => Categorie::class,
                'choice_label' => 'name',
            ])
             ->add('type', ChoiceType::class, [
                'choices' => [
                    'Evenement' => 'Evenement',
                    'Artiste' => 'Artiste',
                    
                    
                ],
                'label' => 'Type d"utilisateur',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
