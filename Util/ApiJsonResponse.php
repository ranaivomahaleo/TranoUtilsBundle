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

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author     ranaivo.razakanirina@atety.com
 */
class ApiJsonResponse
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

        $this->json_response_type = $this->env->getEnv('JSON_RESPONSE_TYPE');
        if (!$this->json_response_type) {
            $this->json_response_type = 'standard';
        } else {
            $this->json_response_type = $this->env->getEnv('JSON_RESPONSE_TYPE');
        } // if
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
     * Allowed status: standard, custom (by default, it is standard)
     * @param $status
     * @param $message
     * @param $results
     */
    private function buildJsonResult($statuscode, $message, $results)
    {
        switch ($this->json_response_type) {
            case 'custom':
                return $results;
                break;
            case 'standard':
            default:
                return ['status' => $statuscode, 'message' => $message, 'results' => $results];
        } // switch

    } // buildJsonResult

    /**
     * Allowed status: standard, custom (by default, it is standard)
     * @param $status
     * @param $message
     * @param $results
     */
    private function buildJsonErrorResult($statuscode, $errormessage)
    {
        // Return the same for custom or standard
        return ['status' => $statuscode, 'message' => $errormessage];
    } // buildJsonResult


    /**
     * @param array $results
     * @return JsonResponse
     */
    public function _200Ok($results = [])
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonResult(200, '', $results),
            JsonResponse::HTTP_OK,
            $headers
        );
    } // _200Ok


    /**
     * @param array $results
     * @return JsonResponse
     */
    public function _201Created($results = [])
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonResult(201, 'Content created', $results),
            JsonResponse::HTTP_CREATED,
            $headers
        );
    } // 201Created


    /**
     * @param array $results
     * @return JsonResponse
     */
    public function _202Accepted($results = [])
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonResult(202, 'Accepted query', $results),
            JsonResponse::HTTP_ACCEPTED,
            $headers
        );
    } // _202Accepted


    /**
     * @param array $results
     * @return JsonResponse
     */
    public function _204NoContent($results = [])
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonResult(204, 'No content returned', $results),
            JsonResponse::HTTP_NO_CONTENT,
            $headers
        );
    } // _204NoContent


    /**
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _400BadRequest($errorstring = '')
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonErrorResult(400, $errorstring),
            JsonResponse::HTTP_BAD_REQUEST,
            $headers
        );
    } // _400BadRequest


    /**
     * @todo To set in other projects
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _401NotAuthorized($errorstring = '')
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonErrorResult(401, $errorstring),
            JsonResponse::HTTP_UNAUTHORIZED,
            $headers
        );
    } // _401NotAuthorized


    /**
     * @todo To set in other projects
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _403StrictNotAuthorized($errorstring = '')
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonErrorResult(403, $errorstring),
            JsonResponse::HTTP_FORBIDDEN,
            $headers
        );
    } // _403StrictNotAuthorized

    /**
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _404NotFound($errorstring = '')
    {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            $this->buildJsonErrorResult(404, $errorstring),
            JsonResponse::HTTP_NOT_FOUND,
            $headers
        );
    } // _404NotFound

}
