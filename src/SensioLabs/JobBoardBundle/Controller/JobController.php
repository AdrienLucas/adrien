<?php

namespace SensioLabs\JobBoardBundle\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

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
     * @Route("/{country}/{contractType}/{slug}/update", name="job_update")
     * @ParamConverter("announcement", class="SensioLabsJobBoardBundle:Announcement")
     * @Template()
     */
    public function updateAction(Request $request, Announcement $announcement)
    {
        $user = $this->getConnectedUser();
        if (!$user || $announcement->getUser() != $user->getUuid()) {
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
            if ($announcement->getValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('An announcement has been modified.')
                    ->setFrom('send@example.com')
                    ->setTo('recipient@example.com')
                    ->setBody(sprintf(
                        'Please review the changes made to <a href="%s">%s</a>.',
                        $this->generateUrl('backend_edit', ['id' => $announcement->getId()]),
                        $announcement->getTitle()
                    ))
                ;
                $this->get('mailer')->send($message);
            }

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

    protected function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken();
        if ($user instanceof AnonymousToken) {
            $user = false;
        } else {
            $user = $user->getApiUser();
        }

        return $user;
    }
}
