<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\View\ViewException;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthenticationException;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \InvalidArgumentException) {
            if (
                Str::contains($exception->getMessage(), 'View [pengadaan.dashboard_procurement] not found') ||
                Str::contains($exception->getMessage(), 'View [gizi.masakan] not found')
            ) {
                return redirect()->route('login');
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 401,
                ],
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
