<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', [
            'projects' => json_decode($this->get('ekreative_redmine_login.client_provider')->get($this->getUser())->get('projects.json')->getBody(), true)['projects']
        ]);
    }
}
