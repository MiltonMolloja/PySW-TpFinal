<?php

namespace App\Form;

use App\Entity\Escribania;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EscribaniaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('direccion')
            ->add('telefono')
            ->add('email')
            ->add('foto')
            ->add('latitud')
            ->add('longitud')
            ->add('estado')
            ->add('nombre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Escribania::class,
        ]);
    }
}
