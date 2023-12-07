<?php

namespace App\Service;


use App\Entity\Reservation;
use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiRequest
{
    private SerializerInterface $serializer;
    private ApiResponse $apiResponse;

    public function __construct(SerializerInterface $serializer, ApiResponse $apiResponse)
    {
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
    }

    public function processRequest(Request $request, string $entityClass): array
    {
        $data = json_decode($request->getContent(), true);
        $entity = $this->serializer->deserialize($request->getContent(), $entityClass, 'json');
        $validationResult = $this->apiResponse->validateEntity($entity);

        if ($validationResult->getStatusCode() !== Response::HTTP_OK) {
            return ['error' => $validationResult];
        }

        return ['data' => $data, 'entity' => $entity];
    }
}