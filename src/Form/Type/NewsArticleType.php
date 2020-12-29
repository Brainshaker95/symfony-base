<?php

namespace App\Form\Type;

use App\Entity\NewsArticle;
use App\Traits\HasTranslator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class NewsArticleType extends AbstractType
{
    use HasTranslator;

    private const MAX_TEXT_LENGTH = 3000;

    /**
     * @param FormBuilderInterface<string> $builder
     * @param array<string> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', Type\TextType::class, [
                'label'       => 'label.title',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.text.empty',
                    ]),
                ],
            ])
            ->add('text', Type\TextareaType::class, [
                'label' => 'label.text',
                'attr'  => [
                    'maxlength' => self::MAX_TEXT_LENGTH,
                ],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'app.error.form.text.empty',
                    ]),
                    new Constraints\Length([
                        'max'        => self::MAX_TEXT_LENGTH,
                        'minMessage' => 'app.error.form.textarea.max',
                    ]),
                ],
            ])
            ->add('privacyAndTerms', Type\CheckboxType::class, [
                'mapped'      => false,
                'label'       => 'label.privacy_and_terms',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsArticle::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
