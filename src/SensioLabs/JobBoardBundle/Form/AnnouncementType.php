<?php

namespace SensioLabs\JobBoardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class AnnouncementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['attr' => ['placeholder' => 'Job title']])
            ->add('company', 'text', ['attr' => ['placeholder' => 'Company']])
            ->add('country')
            ->add('city', 'text', ['attr' => ['placeholder' => 'City']])
            ->add('contractType', 'choice', ['choices' => Announcement::getContractTypes(false), 'empty_value' => 'Type of contract'])
            //FIXME : the field is actually required, but a tinyMCE make the validation behave strangely
            ->add('description', 'textarea', ['required' => false])
            ->add('howToApply', 'text', ['attr' => ['placeholder' => 'Send your resume at ...'], 'required' => false])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SensioLabs\JobBoardBundle\Entity\Announcement',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sensiolabs_jobboardbundle_announcement';
    }
}
