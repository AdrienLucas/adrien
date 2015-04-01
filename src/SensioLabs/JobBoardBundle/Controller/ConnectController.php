<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ConnectController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        return $this->get('security.authentication.entry_point.sensiolabs_connect')->start($request);
    }

    /**
     * @Route("/sln_customizer.js", name="sln_customizer")
     * @Template("SensioLabsJobBoardBundle:Connect:customizer.js.twig")
     */
    public function customizationAction()
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token instanceof TokenInterface  && !($token instanceof AnonymousToken) ? $token->getApiUser() : null;
        //$user = $token instanceof TokenInterface ? $token->getApiUser() : null;

        return array('user' => $user);
    }

    /**
     * @Route("/session/callback", name="session_callback")
     */
    public function sessionCallbackAction(Request $request)
    {
        return $this->get('security.authentication.entry_point.sensiolabs_connect')->start($request);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}
