<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class LoginType extends AbstractType
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
                'attr'        => [
                    'value'   => $options['username'],
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'error.form.register.username.blank',
                    ]),
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'required'    => true,
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
                'label' => 'login',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'authenticate',
            'username'      => '',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
