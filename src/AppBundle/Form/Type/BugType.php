<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BugType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('image', FileType::class, array('label' => 'Image', 'data_class'=>null, 'required' => false))
            ->add('project', EntityType::class, array('label' => 'Project','class' => 'AppBundle:Project', 'empty_data'  => null, 'placeholder' => 'Select project', 'required' => true, 'choice_label' => 'title'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Bug',
            'allow_extra_fields' => true,
        ]);
    }
    public function getName()
    {
        return 'title';
    }

}