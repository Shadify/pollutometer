<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 07/12/17
 * Time: 10:33
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Aqi;

class TrainScheduleController extends Controller
{
    /**
     * @Route("/TrainSchedule")
     */

    public function GetSchedule()
    {
        $url = "http://xmlopen.rejseplanen.dk/bin/rest.exe/multiDepartureBoard?id1=008600617&date=" .
            date("d.m.Y") .
            "&time=00%3A00&useBus=0&format=json";

        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $trains = json_decode($resp, true);
        $trains = $trains['MultiDepartureBoard']['Departure'];

        // Get cURL resource
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pollutometerapi.azurewebsites.net/api/Readings/lastweek");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $readings = json_decode($resp, true);


        for ($i = 0; $i < count($trains); $i++) {
            $closest = 5301590400;
            $time = $trains[$i]['time'];
            $date = $trains[$i]['date'];
            $datesplit = explode(".", $date);
            $datetime = $datesplit[0] . "." . $datesplit[1] . ".20" . $datesplit[2] . " " . $time;
            $trainTimeStamp = strtotime($datetime) + 3600;

            if($trainTimeStamp > time())
            {
                $trains[$i]['direction'] = 0;
                break;
            }

            foreach ($readings as $reading) {
                if (abs($reading['TimeStamp'] - $trainTimeStamp) < abs($closest - $trainTimeStamp))
                    $closest = $reading['TimeStamp'];
            }

            $closestReading = $readings[0];
            foreach ($readings as $reading)
            {
                if($reading['TimeStamp'] == $closest) $closestReading = $reading;
            }
            $trains[$i]['direction'] = $this->getAqi($closestReading);
        }

            $parametersToTwig = array("data" => $trains);

            return $this->render('default/TrainSchedule.html.twig', $parametersToTwig);
        }

    private function getAqi(array $data)
    {
        $aqi = new Aqi();

        $table = array(
            'Co' => array('breakpoints' => [0, 4.4, 4.5, 9.4, 9.5, 12.4, 12.5, 15.4, 15.5, 30.4, 30.5, 40.4, 40.5, 50.4],
                'aq' => [0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]),
            'So' => array('breakpoints' => [0.000, 0.034, 0.035, 0.144, 0.145, 0.224, 0.225, 0.304, 0.305, 0.604, 0.605, 0.804, 0.805, 1.004],
                'aq' => [0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]),
            'No' => array('breakpoints' => [0,0.05,0.08,0.10,0.15,0.20,0.25 ,0.31,0.65, 1.24, 1.25, 1.64, 1.65, 2.04],
                'aq' => [0 ,50 ,51 ,100 ,101 ,150 ,151,200,201, 300, 301, 400, 401, 500])
        );

        $tableObj = json_decode(json_encode($table));

        $arr = [];
        $CO = is_nan($aqi->calculateAQI("Co", $data['Co'], $tableObj)) ? 0 : $aqi->calculateAQI("Co", $data['Co'], $tableObj);
        $SO = is_nan($aqi->calculateAQI("So", $data['So'], $tableObj)) ? 0 : $aqi->calculateAQI("So", $data['So'], $tableObj);
        $NO = is_nan($aqi->calculateAQI("No", $data['No'], $tableObj)) ? 0 : $aqi->calculateAQI("No", $data['No'], $tableObj);

        array_push($arr, $CO, $SO, $NO);
        $max = max($arr);

        return $max;
    }
}