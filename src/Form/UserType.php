<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface<string> $builder
     * @param array<string> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', Type\FileType::class, [
                'required'   => true,
                'attr'       => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.image.empty',
                    ]),
                    new Constraints\File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'save',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
