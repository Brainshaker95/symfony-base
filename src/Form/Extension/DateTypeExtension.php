<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            Type\DateType::class,
            Type\TimeType::class,
            Type\DateTimeType::class,
        ];
    }
}
