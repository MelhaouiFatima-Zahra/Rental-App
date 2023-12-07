<?php

namespace App\Service;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiDefaultResponse
{
    private SerializerInterface $serializer;
    private ApiResponse $apiResponse;
    private UserAccess $userAccess;

    public function __construct(SerializerInterface $serializer, ApiResponse $apiResponse, UserAccess $userAccess)
    {
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
        $this->userAccess = $userAccess;
    }

    public function getAll(ObjectRepository $repository): JsonResponse
    {
        try {
            $result = [];
            foreach ($repository->findAll() as $entity) {
                $result[] = $this->serializer->normalize($entity, null, ['groups' => ['reservation:read']]);
            }

            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            return $this->apiResponse->errorException($e);
        }
    }

    public function getOneById(ObjectRepository $repository, int $id): JsonResponse
    {
        if (null === $entity = $repository->find($id)) {
            $this->apiResponse->notFound();
        }
        try {
            $result = $this->serializer->normalize($entity, null, ['groups' => ['reservation:read']]);
            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            return $this->apiResponse->errorException($e);
        }
    }
}