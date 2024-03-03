<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('nationalite')
            ->add('biography')
            ->add('date_naissance', DateType::class, [
                
                'years' => range(date('Y') - 200, date('Y')), // Customize the date range
                // ... other options
            ])
            ->add('imageArtist', FileType::class, [
                'label' => 'image (PNG file) ',
                'mapped' => true,
                'required' => false,
                
                'constraints' => [
                    new File([
                        'maxSize' => '51200k',
                        
                    ])
                ],
            ])
        ;
        $builder->get('imageArtist')->addModelTransformer(new ImageTransformer());

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
