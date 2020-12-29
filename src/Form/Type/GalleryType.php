<?php

namespace App\Form\Type;

use App\Traits\HasRouter;
use App\Traits\HasTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class GalleryType extends AbstractType
{
    use HasRouter;
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
                'required'   => false,
                'multiple'   => true,
                'data_class' => null,
                'label'      => 'label.images',
                'attr'       => [
                    'data-drag-and-drop' => true,
                    'data-path'          => $this->router->generate('app_api_upload_images'),
                    'data-placeholder'   => $this->translator->trans('placeholder.upload_images'),
                    'data-max-size'      => self::IMAGE_MAX_SIZE,
                    'data-mime-types'    => implode(', ', self::IMAGE_MIME_TYPES),
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.image.empty',
                    ]),
                    new Constraints\All([
                        'constraints' => [
                            new Constraints\File([
                                'maxSize'          => self::IMAGE_MAX_SIZE . 'M',
                                'mimeTypes'        => self::IMAGE_MIME_TYPES,
                                'maxSizeMessage'   => 'app.error.form.max_size.exceeded',
                                'mimeTypesMessage' => 'app.error.form.mime_type.invalid',
                            ]),
                        ],
                    ]),
                ],
            ])
            ->add('privacyAndTerms', Type\CheckboxType::class, [
                'label' => 'label.privacy_and_terms',
                'constraints' => [
                    new Constraints\IsTrue([
                        'message' => 'app.error.form.privacy_and_terms.empty',
                    ]),
                ],
            ])
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
