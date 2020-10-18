<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Traits\HasTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UserType extends AbstractType
{
    use HasTranslator;

    private const IMAGE_MAX_SIZE   = 4;
    private const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];

    /**
     * @param FormBuilderInterface<string> $builder
     * @param array<string> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', Type\FileType::class, [
                'required' => false,
                'mapped'   => false,
                'label'    => 'label.profile_image',
                'attr'     => [
                    'data-placeholder' => $this->translator->trans('placeholder.upload_image'),
                    'data-max-size'    => self::IMAGE_MAX_SIZE,
                    'data-mime-types'  => implode(', ', self::IMAGE_MIME_TYPES),
                ],
                'constraints' => [
                    new Constraints\File([
                        'maxSize'          => self::IMAGE_MAX_SIZE . 'M',
                        'mimeTypes'        => self::IMAGE_MIME_TYPES,
                        'maxSizeMessage'   => 'app.error.form.max_size.exceeded',
                        'mimeTypesMessage' => 'app.error.form.mime_type.invalid',
                    ]),
                ],
            ])
            ->add('theme', Type\ChoiceType::class, [
                'required'    => false,
                'placeholder' => null,
                'label'       => 'label.theme',
                'attr'        => [
                    'class' => 'select--no-clear',
                ],
                'choices' => [
                    'theme.dark'  => 'dark',
                    'theme.light' => 'light',
                ],
            ])
            ->add('conditional_1', Type\CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('conditional_2', Type\CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('conditional_3', Type\CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('conditional_4', Type\CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            // ->add('text', Type\TextType::class, [
            //     'constraints' => new Constraints\NotBlank([
            //         'message' => 'app.error.form.text.empty',
            //     ]),
            // ])
            // ->add('email', Type\EmailType::class, [
            //     'constraints' => [
            //         new Constraints\NotBlank([
            //             'message' => 'app.error.form.email.empty',
            //         ]),
            //         new Constraints\Email([
            //             'message' => 'app.error.form.email.invalid',
            //         ]),
            //     ],
            // ])
            // ->add('date', Type\DateType::class)
            // ->add('time', Type\TimeType::class)
            // ->add('number', Type\NumberType::class)
            // ->add('datetime', Type\DateTimeType::class)
            // ->add('textarea', Type\TextareaType::class, [
            //     'attr' => [
            //         'maxlength' => 50,
            //     ],
            //     'constraints' => [
            //         new Constraints\NotBlank([
            //             'message' => 'app.error.form.text.empty',
            //         ]),
            //         new Constraints\Length([
            //             'max'        => 50,
            //             'minMessage' => 'app.error.form.textarea.max',
            //         ]),
            //     ],
            // ])
            // ->add('select1', Type\ChoiceType::class, [
            //     'multiple' => true,
            //     'choices'  => [
            //         '1' => '1',
            //         '2' => '2',
            //         '3' => '3',
            //         '4' => '4',
            //         '5' => '5',
            //         '6' => '6',
            //         '7' => '7',
            //         '8' => '8',
            //         '9' => '9',
            //     ],
            //     'choice_attr' => function ($value) {
            //         $disabled = false;

            //         if ($value === '2' || $value === '6') {
            //             $disabled = true;
            //         }

            //         return $disabled ? ['disabled' => 'disabled'] : [];
            //     },
            // ])
            // ->add('select2', Type\ChoiceType::class, [
            //     'attr' => [
            //         'data-placeholder' => 'Custom Placeholder',
            //     ],
            //     'choices' => [
            //         '1' => '1',
            //         '2' => '2',
            //         '3' => '3',
            //         '4' => '4',
            //     ],
            //     'choice_attr' => function ($value) {
            //         $disabled = false;

            //         if ($value === '3') {
            //             $disabled = true;
            //         }

            //         return $disabled ? ['disabled' => 'disabled'] : [];
            //     },
            // ])
            // ->add('checkbox', Type\CheckboxType::class, [
            //     'constraints' => new Constraints\IsTrue([
            //         'message' => 'app.error.form.checkbox.empty',
            //     ]),
            // ])
            // ->add('radio', Type\ChoiceType::class, [
            //     'attr' => [
            //         'class' => 'radio--show-label',
            //     ],
            //     'choices' => [
            //         'RadioButton1' => '1',
            //         'RadioButton2' => '2',
            //         'RadioButton3' => '3',
            //     ],
            //     'expanded' => true,
            // ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'label.save',
                'attr'  => [
                    'class' => 'button button--info',
                ],
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
