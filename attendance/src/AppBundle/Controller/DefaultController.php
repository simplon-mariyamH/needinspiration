<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Login;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $students = $this->getEntities('AppBundle:Login');
        return var_dump($students);
    }
    /**
     * @Route("/", name="Login")
     */
    public function verifIdentifiant(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);
        echo $request->request->get('id');
        return $data;

    }
    







     private function getEntities( $entityType ):array
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager->getRepository($entityType)->findAll();
    }
}
