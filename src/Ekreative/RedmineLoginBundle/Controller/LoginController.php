<?php
/**
 * Created by mcfedr on 27/06/15 14:15
 */

namespace Ekreative\RedmineLoginBundle\Controller;

use Ekreative\RedmineLoginBundle\Form\Type\LoginType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginController extends JsonController
{
    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        $form = $this->createForm(
            LoginType::class,
            ['username' => $session->get(Security::LAST_USERNAME)],
            ['action' => $this->generateUrl('login_check')]
        );
        $form->add('submit', SubmitType::class, ['label' => 'Sign In']);

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('@EkreativeRedmineLogin/Login/login.html.twig', [
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function apiLoginAction(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        $this->handleJsonForm($form, $request);
        $data = $form->getData();

        try {
            $user = $this->get('ekreative_redmine_login.provider')->getUserForUsernamePassword(
                $data['username'],
                $data['password']
            );
        }
        catch (AuthenticationException $e) {
            throw new UnauthorizedHttpException(null);
        }

        return new JsonResponse([
            'user' => $user
        ]);
    }
}
