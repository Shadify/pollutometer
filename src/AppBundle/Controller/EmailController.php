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

class EmailController extends Controller
{
    /**
     * @Route("/email")
     */
    public function sendEmail()
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('pollutometer@cumallover.me')
            ->setTo('marc560f@edu.easj.dk')
            ->setBody(
                $this->renderView(
// app/Resources/views/Emails/registration.html.twig
                    'Emails/registration.html.twig',
                    array('name' => "Test")
                ),
                'text/html'
            )/*
* If you also want to include a plaintext version of the message
->addPart(
$this->renderView(
'Emails/registration.txt.twig',
array('name' => $name)
),
'text/plain'
)
*/
        ;

//$mailer->send($message);

// or, you can also fetch the mailer service this way
        $this->get('mailer')->send($message);
    }
}