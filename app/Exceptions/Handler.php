<?php

namespace App\Exceptions;

use Exception;
use Http\Client\Exception\HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            if ($this->isFrontend($request)) {
                return redirect()->guest('login');
            }
            return $this->errorResponse('No se encuentra autenticado', 401, $exception);
        }
        if($exception instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403, $exception);
        }
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontró la URL especificada', 404, $exception);
        }
        if($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado en la petición no es válido', 405, $exception);
        }

        if (preg_match('/InventoryTrait.php/', $exception->getFile())) {
            return $this->errorResponse($exception->getMessage(), 405, $exception);
        }
        // if ($exception->getStatusCode() == 403 && !$this->isFrontend($request)) {
        //     return $this->errorResponse('Acceso denegado: Su cuenta está inactiva, comuníquese con el administrador', 403, $exception);
        // }
        if($exception instanceof HttpException) {
            return $this->errorResponse('', '', $exception);
        }

        if(!$this->isFrontend($request)) {
            return $this->errorResponse('', 500, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {
            return $request->ajax()? response()->json($errors, 422):redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }

        return $this->errorResponse($errors, 422, $e);
    }

    private function isFrontend(Request $request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    private function errorResponse($message, $code , Exception $exception)
    {
        $message = ($message === '')?$exception->getMessage():$message;
        $code = ($code === '')?$exception->getCode():$code;
        $file = $exception->getFile();
        $line = $exception->getLine();

        return response()->json([
            'success' => false,
            'message' => $message,
            'file' => $file,
            'line' => $line
        ], $code);
    }
}
