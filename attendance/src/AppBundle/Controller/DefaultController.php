<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Login;
use AppBundle\Entity\Signin;
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
               
                
              }
            } else {
                $response = [ "server"=>"echec"];
                return new Response(json_encode($response));
            }      
        } 
    }
    // TODO METTRE SERIALIZER DANS USER ET CREE UNE FONCTION OBJECT TO JSON $this=>objectToJson
    // public function objectToJson($object)
    // {
    //     $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
    //     $json = $serializer->serialize($object, 'json', ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    //     return $json;
    // }
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
                $signedIn = $this->alreadySignedInOrNot($id);
                    return new Response($signedIn);
                } else {
                    return new Response("élève inconnu");
                }

            } else {
                return new Response("ID manquant");
            }

        }


     } 
     public function alreadySignedInOrNot( $id) 
     {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Signin');
        $now = new \DateTime('now');
        $currentDate = new \DateTime( $now->format('Y-m-d').' 00:00:00.000000');
        $checkDate = $repository->findby(
            array("date"=>$currentDate,
                  "idUsers"=>$id)
        );
        if (count($checkDate)>= 1 && $checkDate != null){
            
            $PmOrAm = $this->PmOrAm();
            $response = $this->updateDataBase($PmOrAm, $checkDate);
            
            
        } else {
            $PmOrAm = $this->PmOrAm();
            $repository = $this->getDoctrine()->getRepository('AppBundle:Login');
            $eleve = $repository->findOneBy(
                array("id"=>$id)
            );
            // function insert base de données la date presente.
            $this->pushToDB($PmOrAm, $eleve, $currentDate);
            $response =  ["server"=>"success",
                          "message"=>"Votre inscription pour le".$dateNow." matin a bien été prise en compte, Merci."
                         ];

        }
        return $response;
     }
     public function PmOrAm()
     {
         if (date('H') < 12) {
            return  $AM = true;
         } else {
             return $AM = false;
         }

     }
     public function updateDataBase($time, $date)
     {   
         setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
         $em = $this->getDoctrine()->getManager();
         $AM = ['matin' => $date[0]->getMatin()];
         $PM = ['apresmidi' => $date[0]->getApresMidi()];
         $dateNow = strftime("%A %d %B");
         // si matin $time = true, si apres midi $time = false
         if ($time === true) // matin 
         {  
              if ($AM['matin'] != 1){
              $date[0]->setMatin(1);
              $em->flush();
              $message = ["server"=>"success",
                          "message"=>"Votre inscription pour le".$dateNow." matin a bien été prise en compte, Merci."
                         ];  
              $response = json_encode($message,JSON_UNESCAPED_UNICODE);           
             } else { 
             $message = ["server"=>"echec",
                          "message"=>"Vous êtes déjà inscrit pour le ".$dateNow." matin, Merci."
                         ];               
            $response = json_encode($message,JSON_UNESCAPED_UNICODE);
           }
         } 
         if ($time === false){ //apres midi
            if($PM['apresmidi'] != 1){
            $date[0]->setApresMidi(1);
            $em->flush();
            $message = ["server"=>"success",
                          "message"=>"Vous êtes déjà inscrit pour le ".$dateNow." après-midi, Merci."
                         ];
            $response = json_encode($message,JSON_UNESCAPED_UNICODE);
            } else {
             $message = ["server"=>"echec",
                          "message"=>"Vous êtes déjà inscrit pour le ".$dateNow." après-midi, Merci."
                         ];
            $response = json_encode($message, JSON_UNESCAPED_UNICODE);
           }
        }
        return $response;
     }
     public function pushToDB($time, $infoEleve, $currentDate)
     {
         $eleveID = $infoEleve->id;
         $signIn = new Signin();
         $signIn->setIdUsers($eleveID);
         $signIn->setDate($currentDate);
         if($time === true) 
         {
            $signIn->setMatin(1);
            $signIn->setApresMidi(0);
               
         } else {
            $signIn->setMatin(0);
            $signIn->setApresMidi(1);
         }
         $em = $this->getDoctrine()->getManager();
         $em->persist($signIn);
         $em->flush();
     }
     private function getEntities( $entityType ):array
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager->getRepository($entityType)->findAll();
    }
}

 
    