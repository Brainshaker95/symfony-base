<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RegisterType extends AbstractType
{
    private const PASSWORD_MIN_LENGTH = 10;

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
                'label'      => 'label.username',
                'attr'       => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.username.empty',
                    ]),
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'required' => true,
                'mapped'   => false,
                'label'    => 'label.password',
                'attr'     => [
                    'autocomplete' => 'off',
                    'min'          => self::PASSWORD_MIN_LENGTH,
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.password.empty',
                    ]),
                    new Constraints\Length([
                        'min'        => self::PASSWORD_MIN_LENGTH,
                        'max'        => 4096,
                        'minMessage' => 'app.error.form.password.min',
                    ]),
                ],
            ])
            ->add('privacy', Type\CheckboxType::class, [
                'required'    => true,
                'mapped'      => false,
                'label'       => 'label.privacy',
                'constraints' => [
                    new Constraints\IsTrue([
                        'message' => 'app.error.form.privacy.empty',
                    ]),
                ],
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'label.register',
                'attr'  => [
                    'class' => 'button button--info',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'timed_spam' => true,
            'honeypot'   => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
