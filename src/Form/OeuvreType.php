<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Oeuvre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OeuvreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_creation', DateType::class, [
                
                'years' => range(date('Y') - 200, date('Y')), // Customize the date range
                // ... other options
            ])
            ->add('type')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false, // Change as needed
                // ... other options
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
'choice_label' => 'id',
            ])
        ;
        $builder->get('image')->addModelTransformer(new ImageTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
        ]);
    }
}
