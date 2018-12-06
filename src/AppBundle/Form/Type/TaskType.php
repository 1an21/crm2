<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextAreaType::class)
            ->add('image', FileType::class, array('label' => 'Image', 'data_class'=>null, 'required' => false))
            ->add('user', EntityType::class, array('label' => 'Employee','class' => 'AppBundle:User', 'empty_data'  => null, 'placeholder' => 'Select Employee', 'required' => false, 'choice_label' => 'name'))
            ->add('project', EntityType::class, array('label' => 'Project','class' => 'AppBundle:Project', 'empty_data'  => null, 'placeholder' => 'Select project', 'required' => true, 'choice_label' => 'title'))
            ->add('priority', EntityType::class, array('label' => 'Priority','class' => 'AppBundle:Priority', 'empty_data'  => null, 'placeholder' => 'Select priority', 'required' => true, 'choice_label' => 'title'))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Task',
            'allow_extra_fields' => true,
        ]);
    }
    public function getName()
    {
        return 'user';
    }

}