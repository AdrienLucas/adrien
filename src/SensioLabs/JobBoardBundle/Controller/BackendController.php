<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BackendController extends Controller
{
    /**
     * @Route("/backend", name="backend_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $announcements = $this->get('sensiolabs_jobboardbundle.repository.announcement')
            ->getPaginatedResult($request->query->get('status', 'published'), $request->query->get('page', 1));

        return [
            'announcements' => $announcements,
        ];
    }

    /**
     * @Route("/backend/{id}/edit", name="backend_edit")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function editAction(Request $request, Announcement $announcement)
    {
        $form = $this->createForm('sensiolabs_jobboardbundle_announcementadmin', $announcement, [
            'action' => $this->generateUrl('backend_edit', ['id' => $announcement->getId()]),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($announcement);
            $em->flush();

            return $this->redirectToRoute('backend_list');
        }

        return [
            'form' => $form->createView(),
            'announcement' => $announcement,
        ];
    }
}
