<?php

namespace Bbees\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BbeesUserBundle:Default:index.html.twig');
    }
}
