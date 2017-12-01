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
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class EmailSender extends Controller
{
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