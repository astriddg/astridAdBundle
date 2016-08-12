<?php

namespace astrid\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('astridAdBundle:Default:index.html.twig');
    }
}
