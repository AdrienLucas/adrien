<?php

namespace SensioLabs\JobBoardBundle\Twig;

use Symfony\Component\Intl\Intl;

class JobBoardExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('country_name', function ($country) {
                return Intl::getRegionBundle()->getCountryName($country);
            }),
        ];
    }

    public function getName()
    {
        return 'jobboard_extension';
    }
}
