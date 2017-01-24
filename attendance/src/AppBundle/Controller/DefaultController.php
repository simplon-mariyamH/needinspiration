<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Users;
use AppBundle\Entity\Signin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
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
        $students = $this->getEntities('AppBundle:Signin');
        return new Response(var_dump($students));
    }

    /**
     * @Route("/Promo", name="Promo")
     */
    public function getHolidays($year = null)
    {
        if ($year === null)
        {
            $year = intval(date('Y'));
        }

        $easterDate  = easter_date($year);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear   = date('Y', $easterDate);

        $holidays = array(
            // Dates fixes
            mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
            mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
            mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
            mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
            mktime(0, 0, 0, 8,  15, $year),  // Assomption
            mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
            mktime(0, 0, 0, 11, 11, $year),  // Armistice
            mktime(0, 0, 0, 12, 25, $year),  // Noel

            // Dates variables
            mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
        );

        sort($holidays);

        return $holidays;
    }

    /**
     * @Route("/Users", name="Users")
     */
    public function checkId(Request $request)
    {
        if ($request) 
        {
            if (isset($_POST["email"]) && isset($_POST["motdepasse"])){
            $email = $_POST["email"];
            $motdepasse = $_POST["motdepasse"];
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
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
                $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
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
     private function alreadySignedInOrNot( $id) 
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
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $eleve = $repository->findOneBy(
                array("id"=>$id)
            );
            // function insert base de données la date présente.
            $moment = ($PmOrAm === true) ? " matin" : " après-midi";
            $this->pushToDB($PmOrAm, $eleve, $currentDate);
            $response =  ["server"=>"success",
                          "message"=>"Votre inscription pour le ".strftime("%A %d %B").$moment." a bien été prise en compte, Merci."
                         ];

        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
     }
     private function PmOrAm()
     {
         if (date('H') < 12) {
            return  $AM = true;
         } else {
             return $AM = false;
         }

     }
     private function updateDataBase($time, $date)
     {   
         
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
                          "message"=>"Votre inscription pour le ".$dateNow." matin a bien été prise en compte, Merci."
                         ];  
              $response = $message;           
             } else { 
             $message = ["server"=>"echec",
                          "message"=>"Vous êtes déjà inscrit pour le ".$dateNow." matin, Merci."
                         ];               
            $response = $message;
           }
         } 
         if ($time === false){ //apres midi
            if($PM['apresmidi'] != 1){
            $date[0]->setApresMidi(1);
            $em->flush();
            $message = ["server"=>"success",
                          "message"=>"Votre inscription pour le ".$dateNow." après-midi a bien été prise en compte, Merci."
                         ];
            $response = $message;
            } else {
             $message = ["server"=>"echec",
                          "message"=>"Vous êtes déjà inscrit pour le ".$dateNow." après-midi, Merci."
                         ];
            $response = $message;
           }
        }
        return $response;
     }
     private function pushToDB($time, $infoEleve, $currentDate)
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
       /**
     * @Route("/Todaysignatures", name="Todaysignatures")
     */
     public function Todaysignatures()
     {   
         $now = new \DateTime('now');
         $todayDate = new \Datetime($now->format('Y-m-d').' 00:00:00.000000');
         $repository = $this->getDoctrine()->getRepository('AppBundle:Signin');
         $attendants = $repository->findBy(
             array("date"=>$todayDate)
         );
         var_dump($attendants);
     }

     private function getEntities( $entityType ):array
    {
        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager->getRepository($entityType)->findAll();
    }
}

 
    