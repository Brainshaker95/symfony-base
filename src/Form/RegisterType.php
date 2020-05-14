<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface<string> $builder
     * @param array<string> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', Type\TextType::class, [
                'required'    => true,
                'empty_data'  => '',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'error.form.register.username.blank',
                    ]),
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'required'    => true,
                'mapped'      => false,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'error.form.register.password.blank',
                    ]),
                    new Constraints\Length([
                        'min'        => 10,
                        'max'        => 4096,
                        'minMessage' => 'error.form.register.password.min',
                    ]),
                ],
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'register',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
