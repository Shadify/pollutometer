<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AllDataController extends Controller
{
    /**
     * @Route("/AllDataReadings", name="AllData")
     */

    public function GetAllData()
    {
        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pollutometerapi.azurewebsites.net/api/Readings/lastweek");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $data = json_decode($resp, true);


        usort($data, function($a,$b){
            return $a['TimeStamp'] - $b['TimeStamp'];
        });

        foreach($data as $index => $item)
        {
            $data[$index]['TimeStamp'] = gmdate("l jS \of F Y h:i:s A", $item['TimeStamp']);
        }

        $parametersToTwig = array("data" => $data);

        return $this->render('default/AllDataPage.html.twig',$parametersToTwig);

    }
}