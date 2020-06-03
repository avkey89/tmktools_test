<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('lastName')
            ->add('email')
            ->add('active')
            ->add('role', EntityType::class, [
                'multiple' => true,
                'class' => Role::class,
                'choice_label' => 'name',
                'choice_value'  => 'id',
                'data' => $options["data"]->getRoleObject()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            "allow_add" => true,
            "allow_extra_fields" => true
        ]);
    }
}
