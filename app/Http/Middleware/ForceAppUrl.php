<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class ForceAppUrl
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = $this->configuredUrl();

        URL::useOrigin($url);
        URL::forceScheme($this->scheme($url));

        return $next($request);
    }

    private function configuredUrl(): string
    {
        $url = trim((string) config('app.url'));
        $parts = parse_url($url);

        if (
            filter_var($url, FILTER_VALIDATE_URL) === false
            || $parts === false
            || ! isset($parts['scheme'], $parts['host'])
            || $parts['host'] === ''
            || ! in_array(strtolower($parts['scheme']), ['http', 'https'], true)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])
        ) {
            throw new RuntimeException(
                'APP_URL must be an absolute HTTP or HTTPS URL with a host and no credentials, query or fragment.'
            );
        }

        if ($this->requiresHttps() && strtolower($parts['scheme']) !== 'https') {
            throw new RuntimeException(
                'APP_URL must use HTTPS outside the local and testing environments.'
            );
        }

        return strtolower($parts['scheme']).'://'.substr($url, strlen($parts['scheme']) + 3);
    }

    private function scheme(string $url): string
    {
        return strtolower((string) parse_url($url, PHP_URL_SCHEME));
    }

    private function requiresHttps(): bool
    {
        return ! app()->environment(['local', 'testing']);
    }
}
