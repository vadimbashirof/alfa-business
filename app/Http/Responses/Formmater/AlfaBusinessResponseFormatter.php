<?php

namespace App\Http\Response\Formatter\APIv3\Merchant;

use App\Http\Response\Formatter\ApiResponseFormatterInterface;
use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessAccessTokenErrorException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessHttpClientException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFountTransactionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AlfaBusinessResponseFormatter implements ApiResponseFormatterInterface
{
    public function format(Request $request, SymfonyResponse $httpResponse, bool $success = true, string $code = '0'): SymfonyResponse
    {
        $errors = [];
        $data = [];
        $data['success'] = true;

        $response = $this->getDocumentsByResponse($httpResponse);
        $isDocumentsResponse = $this->responseIsDocumentsDTO($httpResponse);

        if (!$isDocumentsResponse) {
            return $httpResponse;
        }

        if ($response && is_array($response)) {
            foreach ($response as $document) {
                $exception = $document->getException();

                if ($exception === null) {
                    continue;
                }

                if (
                    $exception instanceof AlfaBusinessValidationNotFountTransactionException ||
                    $exception instanceof AlfaBusinessHttpClientException ||
                    $exception instanceof AlfaBusinessAccessTokenErrorException
                ) {
                    $data['success'] = false;
                    $httpResponse->setStatusCode(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
                    $transactionDTO = $document->getTransactionDTO();
                    $errors[] = [
                        'message' => $exception->getMessage(),
                        'uniqueKey' => $transactionDTO->generateUniqueKey(),
                        'transactionId' => $transactionDTO->getTransactionId(),
                    ];
                }
            }
        }

        if ($errors) {
            $data['errors'] = $errors;
        }

        if ($httpResponse instanceof JsonResponse) {
            $httpResponse->setData($data);
        } elseif ($httpResponse instanceof Response) {
            $httpResponse->setContent($data);
        }

        return $httpResponse;
    }

    private function responseIsDocumentsDTO(SymfonyResponse $httpResponse): bool
    {
        $response = $this->getDocumentsByResponse($httpResponse);
        $isDocumentResponse = false;
        if ($response && is_array($response)) {
            foreach ($response as $item) {
                if ($item instanceof DocumentDTO) {
                    $isDocumentResponse = true;
                } else {
                    return false;
                }
            }
        }
        return $isDocumentResponse;
    }

    private function getDocumentsByResponse(SymfonyResponse $httpResponse): mixed
    {
        $documents = [];
        if ($httpResponse instanceof JsonResponse) {
            $documents = $httpResponse->getData(true);
        } elseif ($httpResponse instanceof Response) {
            $documents = $httpResponse->getOriginalContent();
        }

        return $documents;
    }
}
