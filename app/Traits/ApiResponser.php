<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait ApiResponser
{
    private function successResponse($data, $statusCode, $metaMessage)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => true,
                    'code' => 20000,
                    'message' => $metaMessage ?? 'Request success',
                ],
                'data' => $data,
            ],
            $statusCode
        );
    }

    protected function errorResponse($errorMessage, $statusCode, $metaCode)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => false,
                    'code' => $metaCode,
                    'message' => $errorMessage,
                ],
                'data' => null,
            ],
            $statusCode,
        );
    }

    protected function showAll(Collection $collection, $statusCode = null, $metaMessage = null)
    {
        return $this->successResponse(
            $collection->values(),
            $statusCode ?? 200,
            $metaMessage,
        );
    }

    protected function showOne($object, $statusCode = null, $metaMessage = null)
    {
        return $this->successResponse(
            $object,
            $statusCode ?? 200,
            $metaMessage,
        );
    }

    protected function showPaginate($resultKey, Collection $resultValues, Collection $paginateCollection, $statusCode = null, $metaMessage = null)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => true,
                    'code' => 20000,
                    'message' => $metaMessage ?? 'Request success',
                ],
                'data' => [
                    $resultKey => $resultValues->values(),
                    'page_info' => [
                        'last_page' => $paginateCollection['last_page'],
                        'current_page' => $paginateCollection['current_page'],
                        'path' => $paginateCollection['path'],
                        'total' => $paginateCollection['total']
                    ]
                ]
            ],
            $statusCode ?? 200
        );
    }
}
