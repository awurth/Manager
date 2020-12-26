<?php

namespace App\Action\Traits;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait ResponseTrait
{
    /**
     * @param \SplFileInfo|string $file
     * @param string|null         $fileName
     * @param string              $disposition
     *
     * @return BinaryFileResponse
     */
    protected function file($file, ?string $fileName = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName ?? $response->getFile()->getFilename());

        return $response;
    }

    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }
}
