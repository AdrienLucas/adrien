<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BackendController extends Controller
{
    /**
     * @Route("/backend", name="backend_list")
     * @Security("has_role('ROLE_CONNECT_USER')")
     * @Template()
     */
    public function listAction(Request $request, $announcementPerPages = 25)
    {
        $repo = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement');
        $announcements = $repo->findBy(['published' => false]);

        $announcements = $this->get('knp_paginator')->paginate($announcements, $request->query->get('page', 1), $announcementPerPages);

        return [
            'announcements' => $announcements,
        ];
    }

    /**
     * @Route("/backend/edit", name="backend_edit")
     * @Template()
     */
    public function editAction(Request $request)
    {
        return [];
    }
}
