<?php

namespace Lehna\QueryBuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for querying COI sampling coverage
 *
 * @Route("/")
 * @Security("has_role('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class DefaultController extends Controller
{
    /**
   * @Route("/", name="query_index")
   * Index : render query form interface
   */
    public function indexAction()
    {
        return $this->render('@LehnaQueryBuilder/Default/index.html.twig');
    }
}
