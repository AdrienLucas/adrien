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
            throw $this->createNotFoundException('Announcement not found in session.');
        }

        return [
            'announcement' => $announcement,
        ];
    }

    /**
     * @Route("/update", name="job_update")
     * @Template()
     */
    public function updateAction(Request $request)
    {
        return array();
    }
}
