<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SensioLabs\JobBoardBundle\Entity\Announcement;
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
        $this->get('jobboard.viewscount.listener')->setEnabled('List');

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
    public function manageAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getApiUser();
        /* @var EntityRepository $repo */
        $announcements = $this->get('sensiolabs_jobboardbundle.repository.announcement')
            ->findByUserPaginated($user->getUuid(), $request->query->get('page', 1));

        return [
            'announcements' => $announcements,
        ];
    }
}
