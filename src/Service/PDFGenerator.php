<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 11.05.18
 * Time: 16:55
 */

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\Templating\EngineInterface;

class PDFGenerator
{
    private $generator;

    private $engine;

    public function __construct(Pdf $generator, EngineInterface $engine)
    {
        $this->generator = $generator;
        $this->engine = $engine;
    }

    public function generatePDF($customer, $services, $totalSum)
    {
        $html = $this->engine->render('emails/invoice.html.twig', [
            'customer' => $customer,
            'services' => $services,
            'totalSum' => $totalSum
        ]);
        $pdf = $this->generator->getOutputFromHtml($html);
        return $pdf;
    }
}