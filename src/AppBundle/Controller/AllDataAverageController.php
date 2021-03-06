<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AllDataAverageController extends Controller
{
    /**
     * @Route("/AllDataAverage", name="AllDataAverage")
     */

    public function GetAllDataAverage()
    {
        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pollutometerapi.azurewebsites.net/api/Readings");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $data = json_decode($resp, true);
        $readings = array();
        $results = array();

        usort($data, function($a,$b){
            return $a['TimeStamp'] - $b['TimeStamp'];
        });

        foreach($data as $index => $item)
        {
            $data[$index]['TimeStamp'] = gmdate('d F l', $item['TimeStamp']);
            $readings[$data[$index]['TimeStamp']][] = $data[$index];
        }

        foreach ($readings as $key => $item)
        {
            $gasAverage = array('Co' => 0, 'No' => 0, 'So' => 0);
            foreach ($readings[$key] as $index => $values)
            {
                $gasAverage['Co'] += $readings[$key][$index]['Co'];
                $gasAverage['No'] += $readings[$key][$index]['No'];
                $gasAverage['So'] += $readings[$key][$index]['So'];

                if($index === count($readings[$key]) - 1)
                {
                    $gasAverage['Co'] /= $index + 1;
                    $gasAverage['No'] /= $index + 1;
                    $gasAverage['So'] /= $index + 1;
                }
                $results[$key] = $gasAverage;
            }
        }

        $data = json_encode($results);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}