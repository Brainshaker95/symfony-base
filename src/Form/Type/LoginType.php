<?php

namespace App\Form\Type;

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
                'label'      => 'label.username',
                'empty_data' => '',
                'attr'       => [
                    'autocomplete' => 'off',
                    'value'        => $options['username'],
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'required' => true,
                'label'    => 'label.password',
                'attr'     => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('_remember_me', Type\CheckboxType::class, [
                'label'    => 'label.remember_me',
                'required' => false,
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'label.login',
                'attr'  => [
                    'class' => 'button button--info',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'authenticate',
            'username'      => '',
            'timed_spam'    => true,
            'honeypot'      => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
