<?php

namespace SensioLabs\JobBoardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class AnnouncementAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publishedAt', 'text', ['required' => false])
            ->add('endedAt', 'text', ['required' => false])
            ->add('valid', 'text', ['required' => false])
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
        return 'sensiolabs_jobboardbundle_announcementadmin';
    }

    public function getParent()
    {
        return 'sensiolabs_jobboardbundle_announcement';
    }
}
