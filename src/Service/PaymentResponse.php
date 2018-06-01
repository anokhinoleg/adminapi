<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 24.04.18
 * Time: 11:32
 */

namespace App\Service;

use App\Entity\PaymentHistory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class PaymentResponse
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createResponse(array $payments, array $context)
    {
        $data = $this->serializer->serialize(
            $payments,
            'json',
            SerializationContext::create()
                ->setGroups(
                    $context
                )
        );
        $response = new Response($data, 201);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}