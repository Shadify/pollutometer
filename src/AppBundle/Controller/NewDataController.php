<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 11/23/17
 * Time: 10:21 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Aqi;
use AppBundle\Utils\EmailSender;

class NewDataController extends Controller
{
    /**
     * @Route("/latest")
     */

    public function getLatestData(Aqi $aqi, EmailSender $emailSender)
    {
        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pollutometerapi.azurewebsites.net/api/Readings/latest");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $data = json_decode($resp, true);
        $data['TimeStamp'] = gmdate("l jS \of F Y h:i:s A", $data['TimeStamp']);
        $data = json_encode($data);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        $data = json_decode($resp, true);
        if($aqi >= 151) $emailSender->sendEmail($data);

        return $response;

    }

}
