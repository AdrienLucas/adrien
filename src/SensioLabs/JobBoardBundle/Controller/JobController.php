<?php

namespace SensioLabs\JobBoardBundle\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class JobController extends Controller
{
    /**
     * @Route("/{country}/{contractType}/{slug}", name="job_show")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function showAction(Announcement $announcement)
    {
        return [
            'announcement' => $announcement,
        ];
    }

    /**
     * @Route("/{country}/{contractType}/{slug}/preview", name="job_preview")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function previewAction(Announcement $announcement)
    {
        return [
            'announcement' => $announcement,
        ];
    }

    /**
     * @Route("/post", name="job_post")
     * @Route("/_update", name="_job_update", defaults={"update" = true})
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
     * @Security("has_role('ROLE_CONNECT_USER')")
     * @Route("/{country}/{contractType}/{slug}/update", name="job_update")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function updateAction(Request $request, Announcement $announcement)
    {
        $user = $this->container->get('security.context')->getToken()->getApiUser();

        if ($announcement->getUser() != $user->getUuid()) {
            throw $this->createNotFoundException('Announcement not found.');
        }

        $form = $this->createForm('sensiolabs_jobboardbundle_announcement', $announcement, [
            'action' => $this->generateUrl('job_update', [
                'country' => $announcement->getCountry(),
                'contractType' => $announcement->getContractType(),
                'slug' => $announcement->getSlug(),
            ]),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($announcement);
            $em->flush();

            return $this->redirect($this->generateUrl('job_preview', [
                'country' => $announcement->getCountry(),
                'contractType' => $announcement->getContractType(),
                'slug' => $announcement->getSlug(),
            ]));
        }

        return [
            'form' => $form->createView(),
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

        if (is_null($this->user->getUuid())) {
            throw new \Exception('You have to be authenticated to publish.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($announcement);
        $em->flush();

        return array();
    }

    /**
     * @Security("has_role('ROLE_CONNECT_USER')")
     * @Route("/{country}/{contractType}/{slug}/pay", name="job_pay")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function payAction(Announcement $announcement)
    {

        $this->getConnectedAnnouncementOwner($announcement);

        //$announcement->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($announcement);
        $em->flush();

        return array();
    }

    /**
     * @Security("has_role('ROLE_CONNECT_USER')")
     * @Route("/{country}/{contractType}/{slug}/delete", name="job_delete")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function deleteAction(Announcement $announcement)
    {
        $this->getConnectedAnnouncementOwner($announcement);

        $em = $this->getDoctrine()->getManager();
        $em->remove($announcement);
        $em->flush();

        return $this->redirect($this->generateUrl('manage'));
    }

    public function getConnectedAnnouncementOwner(Announcement $announcement)
    {
        $user = $this->container->get('security.context')->getToken()->getApiUser();
        if ($announcement->getUser() != $user->getUuid()) {
            throw $this->createNotFoundException('Announcement not found.');
        }

        return $user;
    }
}
