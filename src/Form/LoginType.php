<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'required'   => true,
                'empty_data' => '',
                'attr'       => [
                    'autocomplete' => 'off',
                    'value'        => $options['username'],
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'required' => true,
                'attr'     => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('_remember_me', Type\CheckboxType::class, [
                'required' => false,
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
