<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SensioLabs\JobBoardBundle\Entity\AnnouncementRepository;
use SensioLabs\JobBoardBundle\Form\AnnouncementFiltersType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $this->get('jobboard.viewscount.listener')->setEnabled();

        /** @var AnnouncementRepository $repo */
        $repo = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement');

        $filtersForm = $this->createForm(new AnnouncementFiltersType(), null, [
            'method' => 'GET',
            'action' => $this->generateUrl('homepage'),
        ]);
        $filtersForm->handleRequest($request);

        $announcements = $repo->filterAll(
            $filtersForm->isValid() ? $filtersForm->getData() : array(),
            $request->query->get('page', 1)
        );

        if ($request->isXmlHttpRequest()) {
            if (sizeof($announcements) > 0) {
                return $this->render('SensioLabsJobBoardBundle:Includes:job_container.html.twig', [
                    'announcements' => $announcements,
                ]);
            } else {
                return Response::create('', Response::HTTP_NO_CONTENT);
            }
        }

        return [
            'filtersForm' => $filtersForm->createView(),
            'announcements' => $announcements,
            'countries' => $repo->getCountriesCount(),
            'contractTypes' => $repo->getContractTypesCount(),
        ];
    }

    /**
     * @Route("/manage", name="manage")
     * @Security("has_role('ROLE_CONNECT_USER')")
     * @Template()
     */
    public function manageAction(Request $request, $announcementPerPages = 25)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getApiUser();

        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement');
        $announcements = $repo->findBy(['user' => $user->getUuid()]);

        $announcements = $this->get('knp_paginator')->paginate($announcements, $request->query->get('page', 1), $announcementPerPages);

        return [
            'announcements' => $announcements,
        ];
    }
}
