<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_home")
 */
class HomeAction
{
    public function __invoke(Request $request, HttpKernelInterface $httpKernel): Response
    {
        $subRequest = $request->duplicate(null, null, ['_controller' => ListProjectsAction::class]);
        return $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
