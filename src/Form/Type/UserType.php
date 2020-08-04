<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    /**
     * @var int
     */
    protected $maxSize = 4;

    /**
     * @var array<string>
     */
    protected $mimeTypes = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router     = $router;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface<string> $builder
     * @param array<string> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var array<mixed>
         */
        $data = $options['data'];

        $builder
            ->add('image', Type\FileType::class, [
                'required' => false,
                'label'    => 'label.profile_image',
                'attr'     => [
                    'data-placeholder' => $this->translator->trans('placeholder.upload_image'),
                    'data-max-size'    => $this->maxSize,
                    'data-mime-types'  => implode(', ', $this->mimeTypes),
                ],
                'constraints' => [
                    new Constraints\File([
                        'maxSize'          => $this->maxSize . 'M',
                        'mimeTypes'        => $this->mimeTypes,
                        'maxSizeMessage'   => 'app.error.form.image.max_size.exceeded',
                        'mimeTypesMessage' => 'app.error.form.image.mime_type.invalid',
                    ]),
                ],
            ])
            ->add('theme', Type\ChoiceType::class, [
                'required'    => false,
                'placeholder' => null,
                'label'       => 'label.theme',
                'attr'        => [
                    'class' => 'select--no-clear',
                    'value' => $data['theme'],
                ],
                'choices' => [
                    'theme.dark'  => 'dark',
                    'theme.light' => 'light',
                ],
            ])
            ->add('conditional_1', Type\CheckboxType::class, [
                'required'    => false,
            ])
            ->add('conditional_2', Type\CheckboxType::class, [
                'required'    => false,
            ])
            // ->add('text', Type\TextType::class)
            // ->add('date', Type\DateType::class)
            // ->add('time', Type\TimeType::class)
            // ->add('number', Type\NumberType::class)
            // ->add('datetime', Type\DateTimeType::class)
            // ->add('textarea', Type\TextareaType::class, [
            //     'required' => true,
            //     'attr' => [
            //         'maxlength' => 50,
            //     ],
            //     'constraints' => [
            //         new Constraints\Length([
            //             'max'        => 50,
            //             'minMessage' => 'app.error.form.textarea.max',
            //         ]),
            //     ],
            // ])
            // ->add('select1', Type\ChoiceType::class, [
            //     'required' => true,
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

            //         if ($value === 'friend') {
            //             $disabled = true;
            //         }

            //         return $disabled ? ['disabled' => 'disabled'] : [];
            //     },
            // ])
            // ->add('checkbox', Type\CheckboxType::class)
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

    public function getBlockPrefix(): string
    {
        return '';
    }
}
