<?php

namespace Lehna\QueryBuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@LehnaQueryBuilder/Default/index.html.twig');
    }
}
