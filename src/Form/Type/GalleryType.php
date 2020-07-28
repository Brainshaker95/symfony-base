<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Contracts\Translation\TranslatorInterface;

class GalleryType extends AbstractType
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
                    'data-max-size'      => $this->maxSize,
                    'data-mime-types'    => implode(', ', $this->mimeTypes),
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.image.empty',
                    ]),
                    new Constraints\All([
                        'constraints' => [
                            new Constraints\File([
                                'maxSize'          => $this->maxSize . 'M',
                                'mimeTypes'        => $this->mimeTypes,
                                'maxSizeMessage'   => 'app.error.form.image.max_size.exceeded',
                                'mimeTypesMessage' => 'app.error.form.image.mime_type.invalid',
                            ]),
                        ],
                    ]),
                ],
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'label.save',
                'attr'  => [
                    'class' => 'button--info',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
