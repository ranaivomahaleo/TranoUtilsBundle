<?php
/*
 * This file is part of the TranoUtilsBundle package.
 *
 * (c) atety <https://www.atety.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Trano\UtilsBundle\Util;

use Symfony\Component\HttpFoundation\Response;

class ExtendedResponse
{

    private $json_response_type = 'standard';

    /**
     * @var Env
     */
    private $env;

    /**
     * ApiJsonResponse constructor.
     * @param Env $env
     */
    public function __construct(Env $env)
    {
        $this->env = $env;
    } // __construct


    private function buildAccessControlHeaders()
    {
        $headers = [];

        // Always set to insure HSTS
        $headers['Strict-Transport-Security'] = 'max-age=31536000';
        if ($this->env->getEnv('HEADER_STRICT_TRANSPORT_SECURITY')) {
            $headers['Strict-Transport-Security'] = $this->env->getEnv('HEADER_STRICT_TRANSPORT_SECURITY');
        }  // if

        // Set to no-cache, no-store, must-revalidate
        // by default to avoid storing information in the cache of the browser
        $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
        if ($this->env->getEnv('HEADER_CACHE_CONTROL')) {
            $headers['Cache-Control'] = $this->env->getEnv('HEADER_CACHE_CONTROL');
        }  // if

        // Set the Pragma by default to no-cache. This is related to Cache-Control header
        $headers['Pragma'] = 'no-cache';
        if ($this->env->getEnv('HEADER_PRAGMA')) {
            $headers['Pragma'] = $this->env->getEnv('HEADER_PRAGMA');
        }  // if

        // Do not include a referrer info by default to avoid tracking
        $headers['Referrer-Policy'] = 'no-referrer';
        if ($this->env->getEnv('HEADER_REFERRER_POLICY')) {
            $headers['Referrer-Policy'] = $this->env->getEnv('HEADER_REFERRER_POLICY');
        }  // if

        // Enforce the MIME type related to the browser by default
        $headers['X-Content-Type-Options'] = 'nosniff';
        if ($this->env->getEnv('HEADER_X_CONTENT_TYPE_OPTIONS')) {
            $headers['X-Content-Type-Options'] = $this->env->getEnv('HEADER_X_CONTENT_TYPE_OPTIONS');
        }  // if

        // Load resources types from the own domain and same origin. Wildcard may be used such as *.example.com
        $headers['Content-Security-Policy'] = "default-src 'self'";
        if ($this->env->getEnv('HEADER_CONTENT_SECURITY_POLICY')) {
            $headers['Content-Security-Policy'] = $this->env->getEnv('HEADER_CONTENT_SECURITY_POLICY');
        }  // if

        // Deny framing by default
        $headers['X-Frame-Options'] = "deny";
        if ($this->env->getEnv('HEADER_X_FRAME_OPTIONS')) {
            $headers['X-Frame-Options'] = $this->env->getEnv('HEADER_X_FRAME_OPTIONS');
        }  // if

        // Configure by default the browser with XSS filters
        $headers['X-XXS-Protection'] = "1; mode=block";
        if ($this->env->getEnv('HEADER_X_XXS_PROTECTION')) {
            $headers['X-XXS-Protection'] = $this->env->getEnv('HEADER_X_XXS_PROTECTION');
        }  // if


        if ($this->env->getEnv('ALLOWED_ORIGIN')) {
            $headers['Access-Control-Allow-Origin'] = $this->env->getEnv('ALLOWED_ORIGIN');
        } // if
        if ($this->env->getEnv('ALLOWED_METHODS')) {
            $headers['Access-Control-Allow-Methods'] = $this->env->getEnv('ALLOWED_METHODS');
        } // if
        if ($this->env->getEnv('ALLOWED_HEADERS')) {
            $headers['Access-Control-Allow-Headers'] = $this->env->getEnv('ALLOWED_HEADERS');
        } // if
        return $headers;
    } // buildAccessControlHeaders


    /**
     * @param Response $response
     */
    public function encapsulateHeaders($response)
    {
        $headers = $this->buildAccessControlHeaders();

        // Example of headers to set
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        foreach ($headers as $headerKey => $header) {
            $response->headers->set($headerKey, $header);
        } // foreach

        return $response;
    }

}
