<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 11.05.18
 * Time: 17:14
 */

namespace App\Service;

class MessageGenerator
{
    /**
     * @param $data
     * @return \Swift_Mime_SimpleMessage
     */
    public function createMessageWithPDF($pdf, $subject, $from, $to)
    {
        $filename = sprintf('invoice-%s.pdf', date('Y-m-d'));
        $attachment = new \Swift_Attachment($pdf, $filename, 'application/pdf');

        /** @var \Swift_Mime_SimpleMessage */
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->attach($attachment);
        ;
        return $message;
    }
}