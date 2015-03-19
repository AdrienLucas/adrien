<?php

namespace SensioLabs\JobBoardBundle\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    /**
     * @Route("/show", name="job_show")
     * @Template()
     */
    public function showAction()
    {
        return array();
    }

    /**
     * @Route("/{country}/{contractType}/{slug}/preview", name="job_preview")
     * @Template()
     */
    public function previewAction()
    {
        $announcement = $this->get('session')->get('announcement_preview');

        if (!$announcement instanceof Announcement) {
            //@todo : 404 template
            throw $this->createNotFoundException('Announcement not found in session.');
        }

        return [
            'announcement' => $announcement,
        ];
    }

    /**
     * @Route("/post", name="job_post")
     * @Route("/update", name="job_update", defaults={"update" = true})
     * @Template()
     */
    public function postAction(Request $request, $update = false)
    {
        if ($update) {
            $announcement = $this->get('session')->get('announcement_preview');

            if (!$announcement instanceof Announcement) {
                throw $this->createNotFoundException('Announcement not found in session.');
            }
        } else {
            $announcement = new Announcement();
        }
        $form = $this->createForm('sensiolabs_jobboardbundle_announcement', $announcement, [
            'action' => $this->generateUrl('job_post'),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('session')->set('announcement_preview', $announcement);

            return $this->redirect($this->generateUrl('job_preview', [
                'country' => $announcement->getCountry(),
                'contractType' => $announcement->getContractType(),
                'slug' => $announcement->getSlug(),
            ]));
        }

        return [
            'form' => $form->createView(),
            'updating' => $update,
            'announcement' => $announcement,
        ];
    }

    /**
     * @Route("/order", name="job_order")
     * @Template()
     */
    public function orderAction()
    {

        $announcement = $this->get('session')->get('announcement_preview');

        if (!$announcement instanceof Announcement) {
            throw $this->createNotFoundException('Announcement not found in session.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($announcement);
        $em->flush();

        return array();
    }
}
