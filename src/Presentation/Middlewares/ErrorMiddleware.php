<?php 
namespace Todoist\Presentation\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * Middleware de Erro
     * Envolve toda a aplicação em um tratamento de erros
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'errors' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTrace()
                ]
            ]));
            return $response->withStatus(400);
        }
    }
}