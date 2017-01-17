<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Login;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


$_POST['email'] = "Test_email";
$_POST['motdepasse'] = "Test_motdepasse";

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
     * @Route("/Login", name="Login")
     */
    public function verifIdentifiant(Request $request)
    {
        if ($request) 
        {
            $email = $_POST["email"];
            $motdepasse = $_POST["motdepasse"];
            $repository = $this->getDoctrine()->getRepository('AppBundle:Login');
            $eleve = $repository->findOneBy(
            array('email' => $email , 'motdepasse' => $motdepasse)
            );
            if ($eleve === null)
            {
                $echec = array('server' => 'echec');
                return json_encode($echec);
            } else {
                $user = [];
                foreach ($eleve as $key=>$value) {
                    $user[$key] = $value;
                }
                $response = [
                    "server"=>"success", 
                    "user"=>$user];
                return new Response(json_encode($response));

                // $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
                // $json = $serializer->serialize($eleve, 'json');
                
                /*
                $response = json_encode(["server" => 'success']);
                echo $response;
                return new Response($json);
                */
              }      
        } 
       

    }
     private function getEntities( $entityType ):array
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager->getRepository($entityType)->findAll();
    }
}

 
    