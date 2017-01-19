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
 $_POST["id"] = 1;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $students = $this->getEntities('AppBundle:Login');
        return new Response(var_dump($students));
    }
    /**
     * @Route("/Login", name="Login")
     */
    public function checkId(Request $request)
    {
        if ($request) 
        {
            if (isset($_POST["email"]) && isset($_POST["motdepasse"])){
            $email = $_POST["email"];
            $motdepasse = $_POST["motdepasse"];
            $repository = $this->getDoctrine()->getRepository('AppBundle:Login');
            $eleve = $repository->findOneBy(
            array('email' => $email , 'motdepasse' => $motdepasse)
            );
            if ($eleve === null)
            {
                $echec = array('server' => 'echec');
                return new Response(json_encode($echec));
            } else {
                $user = [];
                foreach ($eleve as $key=>$value) {
                    $user[$key] = $value;
                }
                $response = [
                    "server"=>"success", 
                    "user"=>$user];
                return new Response(json_encode($response));
                // METTRE SERIALIZER DANS USER ET CREE UNE FONCTION OBJECT TO JSON
                // $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
                // $json = $serializer->serialize($eleve, 'json');
                
                /*
                $response = json_encode(["server" => 'success']);
                echo $response;
                return new Response($json);
                */
              }
            } else {
                return new Response("email ou mot de passe manquant");
            }      
        } 
       

    }
       /**
     * @Route("/Signin", name="Signin")
     */
     public function signIn(Request $request)
     {
        // id verif si existe et ugrade la base de données. gerer si l'utilisateur a dejà signé.
        if ($request) 
        {
            if(isset($_POST["id"])){
                $id = $_POST["id"];
                $repository = $this->getDoctrine()->getRepository('AppBundle:Login');
                $eleve = $repository->findOneBy(
                array("id"=>$id)
                );
                if(isset($eleve))
                {
                    return new Response("eleve existant");
                } else {
                    return new Response("élève inconnu");
                }

            } else {
                return new Response("ID manquant");
            }

        }


        return new Response();
     } 
     private function getEntities( $entityType ):array
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager->getRepository($entityType)->findAll();
    }
}

 
    