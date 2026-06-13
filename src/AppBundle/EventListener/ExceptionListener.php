<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;

class ExceptionListener {

    protected $templating;
    protected $kernel;

    public function __construct(Environment $templating, $kernel) {
        $this->templating = $templating;
        $this->kernel = $kernel;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) {
        // provide the better way to display a enhanced error page only in prod environment, if you want
        if ('prod' == $this->kernel->getEnvironment()) {
            // exception object
            $exception = $event->getException();

            // new Response object
            $response = new Response();

            // set response content
            $response->setContent(
                    // create you custom template AcmeFooBundle:Exception:exception.html.twig
                    $this->templating->render(
                            '@App/Exception/exception.html.twig', array('exception' => $exception)
                    )
            );

            // HttpExceptionInterface is a special type of exception
            // that holds status code and header details
            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
            } else {
                $response->setStatusCode(500);
            }

            // set the new $response object to the $event
            $event->setResponse($response);
        }
    }

}
