<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ValidationException $exception) {
            if (request()->expectsJson()) {
                return null;
            }

            $redirect = $exception->redirectTo
                ? redirect($exception->redirectTo)
                : redirect()->back();

            return $redirect
                ->withErrors($exception->errors(), $exception->errorBag)
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Verifie les champs du formulaire.',
                ]);
        });

        $exceptions->renderable(function (AuthorizationException $exception) {
            if (request()->expectsJson()) {
                return null;
            }

            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Action non autorisee.',
            ]);

            return response()->view('errors.403', [], 403);
        });

        $exceptions->renderable(function (ThrottleRequestsException $exception) {
            if (request()->expectsJson()) {
                return null;
            }

            $headers = $exception->getHeaders();
            $retryAfter = $headers['Retry-After'] ?? null;
            $retryAfterSeconds = is_numeric($retryAfter) ? (int) $retryAfter : null;

            $message = $retryAfterSeconds
                ? 'Trop de creations d\'affilee. Reessaie dans ' . $retryAfterSeconds . 's.'
                : 'Trop de tentatives. Reessaie plus tard.';

            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => $message,
                ]);
        });

        $exceptions->renderable(function (HttpExceptionInterface $exception) {
            if (request()->expectsJson() || $exception->getStatusCode() !== 403) {
                return null;
            }

            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Action non autorisee.',
            ]);

            return response()->view('errors.403', [], 403);
        });

        $exceptions->renderable(function (PostTooLargeException $exception) {
            if (request()->expectsJson()) {
                return null;
            }

            return redirect()
                ->back()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Fichier trop lourd. Taille max: 10 Mo.',
                ]);
        });
    })->create();
