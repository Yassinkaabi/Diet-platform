<?php 

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Current Password',
                'label_attr' => ['style' => 'font-size: 18px;'], 
                'mapped' => false, // This field is not mapped to the entity
                'required' => true, // Make it optional for profile editing
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label_attr' => ['style' => 'font-size: 14px;'], 
                'first_options' => ['label' => 'New password *'],
                'second_options' => ['label' => 'Confirm Password *'],
                'required' => true, // Make it optional for profile editing
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);    }
}
