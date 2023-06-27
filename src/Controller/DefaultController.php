<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController {
  #[Route("/legals/", name: "legals")]
  public function legals(Request $request) {
    return $this->render("misc/legal-notices." . $request->getLocale() . ".html.twig");
  }
}
