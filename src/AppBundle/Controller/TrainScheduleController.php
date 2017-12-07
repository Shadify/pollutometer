<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 07/12/17
 * Time: 10:33
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrainScheduleController extends Controller
{
    /**
     * @Route("/TrainSchedule")
     */

    public function GetSchedule()
    {
        $client   = new SoapClient( "http://traindata.dsb.dk/stationdeparture/Service.asmx?WSDL" );
        $params   = array( 'request' => array( 'UICNumber' => '8600617' ) );
        $response = $client->GetStationQueue( $params );
        $data = $response->GetStationQueueResult->Trains->Queue;
        $parametersToTwig = array("data" => $data);

        return $this->render('default/TrainSchedule.html.twig',$parametersToTwig);
    }
}