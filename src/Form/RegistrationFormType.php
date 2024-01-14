<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email')
            // ->add('imageFile', VichImageType::class, [
            //     'label' => 'photo de profile',
            //     'label_attr' => [
            //         'class' => 'form-label mt-4'
            //     ],
            //     'required' => false
            // ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
                'expanded' => true,
                'multiple' => false,
            ])         
            ->add('age')
            ->add('weight')
            ->add('height')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label_attr' => ['style' => 'margin-top: 20px;'], 
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                // 'label_attr' => ['style' => 'margin-bottom: 20px;'], 
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'Repeat new password'],
                'mapped' => false, // Not mapped to the entity
                'required' => false, // It's not required for all form submissions
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
