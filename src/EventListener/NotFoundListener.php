<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class NotFoundListener
{
    private $router;
    public function __construct(UrlGeneratorInterface $router){
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Vérifie si l'exception est une NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            // Vérifie si la requête concerne la route '_details' (optionnel)
            $request=$event->getRequest();
            if($request->attributes->get('_route')=='app_profil_details'){
                $session = $request->getSession();
                $session->getFlashBag()->add('error', 'Le participant demandé n\'existe pas.');
            }

            //On redirige
            $response = new RedirectResponse(
                $this->router->generate('app_home')
            );
            $event->setResponse($response);
        }
    }
}
