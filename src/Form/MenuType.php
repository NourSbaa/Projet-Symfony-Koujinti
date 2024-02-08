<?php

namespace App\Form;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Image;
use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class MenuType extends AbstractType


    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('titre')
                ->add('type')
                ->add('nbrcalories')
                ->add('price')
                ->add('image', EntityType::class, [
                    'class' => Image::class,
                    'choice_label' => 'alt',
                ])
            ;
        }
    
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Menu::class,
            ]);
        }
    }