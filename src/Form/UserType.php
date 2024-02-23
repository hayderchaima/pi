<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\Role;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('nom')
            ->add('prenom')
            
            
            ->add('role', ChoiceType::class, [
                'choices' => array_flip(Role::toArray()), // Use the enum class to fetch choices
                // Optionally, you can set a default value:
                'data' => Role::abonne, // Assuming USER is a default role
                // Other options, like labels, required, etc., can be added as needed
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
