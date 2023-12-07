<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiResponse
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return JsonResponse
     */
    public function notFound(): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => 'Not Found'], 400);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function success(array $data): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'data' => $data, 'total' => count($data)]);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function error(string $message): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $message], 500);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function successResponse(string $message): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'message' => $message], Response::HTTP_OK);
    }

    /**
     * @param object $exception
     * @return JsonResponse
     */
    public function errorException(object $exception): JsonResponse
    {
        return $this->error($exception->getMessage());
    }

    public function validateEntity($entity): JsonResponse
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'Entity is valid'], Response::HTTP_OK);
    }
}