<?php

namespace App\Form;

use App\Entity\Category;
use App\Jaar\DataFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SubscriptionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $datafile = $options['datahandler'];
        $categories = $datafile->getContent('categories.json');

        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('categories', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $categories,
                'choice_label' => function($value, $key, $index) {
                    return strtoupper($value);}
            ])
            ->add('Subscribe', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('datahandler');
    }
}
