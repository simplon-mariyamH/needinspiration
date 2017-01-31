<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Student;
use AppBundle\Entity\Signin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


date_default_timezone_set('Europe/Paris');
// date_default_timezone_set('America/Los_Angeles');



class DefaultController extends Controller
{
  private $now;
  private $meridiem;


  private $student;
  private $signin;

  function __construct()
  {
    // $this->now = new \Datetime('now');
    $this->now = new \Datetime('2017-01-15 00:00:00.000000');
    $this->meridiem = $this->now->format('A');
  }


  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request)
  {
    return new Response('HOME');
  }

  /**
   * @Route("/daily-sign", name="daily-sign")
   * Recupération de toutes les signatures.
   */
  public function DailySignAction()
  {
    $repo = $this->getDoctrine()
      ->getManager()
      ->getRepository('AppBundle:Signin');
    $allSign = $repo->findAll();

    return new Response(json_encode($allSign));

  }

  /**
   * @Route("/admin/get/signature/today", name="signature-today")
   * @return Response
   */
  public function getSignatureForToday()
  {
    $signatures = $this->getDoctrine()->getManager()
      ->getRepository('AppBundle:Signin')
      ->findBy( array( 'date' => $this->now ));

    // var_dump($signature->getStudent());
    // $signaturesToday = [];
    foreach ($signatures as $signature) {
      // var_dump($signature);
      $signaturesToday[] = [
        "id" => $signature->id,
        "date" => $signature->date->format('Y-m-d'),
        // "date" => explode(" " ,$signature->date->date)[0]
        "matin" => (isset($signature->matin)) ? $signature->matin : null,
        "apres_midi" => (isset($signature->apres_midi)) ? $signature->apres_midi : null,
        "student_id" => $signature->getStudent()->getId()
      ];
    }
    var_dump($signaturesToday);
    return new Response('OK');
  }

  

  /**
   * @Route("/auth-student", name="auth-student")
   */
  public function AuthStudent()
  {
    $critere = [
      "name"=> $_POST['name'] ,
      "lastname"=> $_POST['lastname'],
      "password"=> $_POST['password']
    ];
    $em = $this->getDoctrine()->getManager();
    $student = $em->getRepository('AppBundle:Student')->findBy($critere);
    if ( !empty($student) ) {
      $student = $student[0];
      if(!$this->checkIfAlreadySigned($student)) {
        $this->dailySignNowAction($student);
        $res = [ "server" => "signed", "msg" => "studend have been signed" ];
      } else {
        if($this->meridiem === 'AM' AND $this->signin->matin === 0) {
          $this->signin->setMatin(1);
          $res = [ 'server'=>'signed', 'msg'=>"have been signed for matin" ];
        } elseif($this->meridiem === 'AM' AND $this->signin->matin === 1) {
          $res = [ 'server'=>'fail', 'msg'=>"already been signed for matin" ];
        }
        if($this->meridiem === 'PM' AND ($this->signin->apresMidi === null || 0))
        {
          $this->signin->setApresMidi(1);
          $res = [ 'server'=>'signed', 'msg'=>"have been signed for apres midi" ];
        } elseif($this->meridiem === 'PM' AND $this->signin->apresMidi === 1) {
          $res = [ 'server'=>'fail', 'msg'=>"already signed for apres midi" ];
        }
        $em->flush();
      }
    } else { $res = [ "server" => "echec", "msg" => "incorrect username or password" ]; }


    /*$template = $this
      ->get('templating')
      ->render('AppBundle:Default:index.html.twig',
        array('json' => json_encode($res)));
    return new Response($template);*/
    return new Response(json_encode($res));
  }


      /* Signe la presence de l'etudiant */
      private function dailySignNowAction($student)
      {
        $em = $this->getDoctrine()->getManager();
        $sign = new Signin();
        $sign->setDate(new \Datetime($this->now->format('Y-m-d').' 00:00:00.000000'));
        if ($this->meridiem === 'AM') { $sign->setMatin(1); }
        if ($this->meridiem === 'PM') { $sign->setApresMidi(1); }
        $sign->setStudent($student);

        $em->persist($sign);
        $em->flush();
      }
      /*
       * Verifie si l'etudiant a déja signé pour la période déterminer (AM/PM)
       * Retourne un booléen
       * Si le booléen vaut 'true' alors la fonction stock la signature dans
       *    une propriété
       */
      private function checkIfAlreadySigned($student)
      {
        $em = $this->getDoctrine()->getManager();
        $critere = $this->createDate();
        $critere['student'] = $student;

        $signin = $em->getRepository('AppBundle:Signin')
          ->findBy( $critere );

        if(isset($signin) && (count($signin) > 0) ) {$this->signin = $signin[0];}
        return (count($signin) > 0) ? true : false;
      }
      /*
       * Créer un tableau associatif correspondant a la période
       *    de la journée et le retourne
       */
      private function createDate()
      {

        $date = new \Datetime($this->now->format('Y-m-d').' 00:00:00.000000');
        /*if ($this->meridiem === 'AM') {
          $res = ['date' => $date, 'matin' => 1];
        } else {
          $res = ['date' => $date, 'apresMidi' => 1];
        }
        $res = ['date' => $date, 'apresMidi' => 1];
        return $res;*/
        return ['date'=>$date];
      }
}

 
    