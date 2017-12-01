<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 01/12/17
 * Time: 10:47
 */

namespace AppBundle\Utils;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Aqi;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class EmailSender extends Controller
{
    public function main()
    {
        $data = $this->getData();
        if(160 >= 151) $this->sendEmail($data);
        sleep(3600);
    }

    public function getData()
    {
        $table = array(
            'Co' => array('breakpoints' => [0, 4.4, 4.5, 9.4, 9.5, 12.4, 12.5, 15.4, 15.5, 30.4, 30.5, 40.4, 40.5, 50.4],
                'aq' => [0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]),
            'So' => array('breakpoints' => [0.000, 0.034, 0.035, 0.144, 0.145, 0.224, 0.225, 0.304, 0.305, 0.604, 0.605, 0.804, 0.805, 1.004],
                'aq' => [0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]),
            'No' => array('breakpoints' => [0,0.05,0.08,0.10,0.15,0.20,0.25 ,0.31,0.65, 1.24, 1.25, 1.64, 1.65, 2.04],
                'aq' => [0 ,50 ,51 ,100 ,101 ,150 ,151,200,201, 300, 301, 400, 401, 500])
        );


        $tableObj = json_decode(json_encode($table));

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

        $aqi = new Aqi();

        $arr = [];
        $CO = is_nan($aqi->calculateAQI("Co", $data['Co'], $tableObj)) ? 0 : $aqi->calculateAQI("Co", $data['Co'], $tableObj);
        $SO = is_nan($aqi->calculateAQI("So", $data['So'], $tableObj)) ? 0 : $aqi->calculateAQI("So", $data['So'], $tableObj);
        $NO = is_nan($aqi->calculateAQI("No", $data['No'], $tableObj)) ? 0 : $aqi->calculateAQI("No", $data['No'], $tableObj);

        array_push($arr, $CO, $SO, $NO);
        $max = max($arr);
        $data['Aqi'] = $max;

        return $data;
    }

    public function sendEmail(array $data)
    {
// Create the Transport
        $transport = (new Swift_SmtpTransport('mail.cock.li', 465, 'ssl'))
            ->setUsername('pollutometer@cumallover.me')
            ->setPassword('Pass123!')
        ;

// Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

// Create a message
        $message = (new Swift_Message('Pollutometer warning ' . date('d/m/Y h:i:s')))
            ->setFrom(['pollutometer@cumallover.me' => 'Pollutometer'])
            ->setTo(['marc560f@edu.easj.dk' => 'A name'])
            ->setBody($this->renderView(
                'emails/warning.html.twig', $data),
                'text/html')
        ;

// Send the message
        $result = $mailer->send($message);
    }
}