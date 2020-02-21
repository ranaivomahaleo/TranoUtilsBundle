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
    }

    private function buildAccessControlHeaders()
    {
        $headers = [];
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


    public function _200Ok($results = []) {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 200, 'message' => '', 'results' => $results],
            JsonResponse::HTTP_OK,
            $headers
        );
    } // _200Ok

    public function _204NoContent($results = []) {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 204, 'message' => 'No content returned', 'results' => $results],
            JsonResponse::HTTP_NO_CONTENT,
            $headers
        );
    }


    public function _400BadRequest($errorstring = '') {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 400, 'message' => $errorstring, 'results' => []],
            JsonResponse::HTTP_BAD_REQUEST,
            $headers
        );
    }


    /**
     * @todo To set in other projects
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _401NotAuthorized($errorstring = '') {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 401, 'message' => $errorstring, 'results' => []],
            JsonResponse::HTTP_UNAUTHORIZED,
            $headers
        );
    }


    /**
     * @todo To set in other projects
     * @param string $errorstring
     * @return JsonResponse
     */
    public function _403StrictNotAuthorized($errorstring = '') {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 403, 'message' => $errorstring, 'results' => []],
            JsonResponse::HTTP_FORBIDDEN,
            $headers
        );
    }

    public function _404NotFound($errorstring = '') {
        $headers = $this->buildAccessControlHeaders();
        return new JsonResponse(
            ['status' => 404, 'message' => $errorstring, 'results' => []],
            JsonResponse::HTTP_NOT_FOUND,
            $headers
        );
    }

}
