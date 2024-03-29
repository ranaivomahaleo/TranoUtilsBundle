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

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @author     ranaivo.razakanirina@atety.com
 */
class HttpRequest
{
    /**
     * @var array
     */
    private $headers = [
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => '1.0'
        ]
    ];

    /**
     * @var array
     */
    private $options = [];


    /**
     * @var bool
     */
    private $verbose = false;


    /**
     * Generate exception = true means that for each http problem, an exception is returned.
     * @var bool
     */
    private $generateException = false;


    /**
     * @var float
     */
    private $timeout = 0;


    /**
     * @var Env
     */
    private $env;


    /**
     * HttpRequest constructor.
     * @param Env $env
     */
    public function __construct(Env $env)
    {
        $this->env = $env;

        // Default false. It returns null in case of error.
        // When verbose is true, the query returns an array with complete info concerning all errors.
        $this->verbose = false;

        // Link HTTP_SSL_VERIFY_PEER from .env variable to verify_peer.
        // By default, verify_peer is true for security reasons.
        switch ($this->env->getEnv('HTTP_SSL_VERIFY_PEER')) {

            case 'inactive':
                $this->options['verify_peer'] = false;
                break;

            case 'active':
            default:
                $this->options['verify_peer'] = true;
                break;
        } // switch

    }


    public function resetHeader()
    {
        $this->headers = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => '1.0'
            ]
        ];
        return $this;
    }

    /**
     * @param array $options
     * @return HttpRequest
     */
    public function setOptions(array $options): HttpRequest
    {
        $this->options = $options;
        return $this;
    } // resetHeader





    public function setHeader($key = '', $value = '')
    {

        if ($key == '' || $value == '') {
            return $this;
        } // if

        if (array_key_exists($key, $this->headers['headers'])) {
            $this->headers['headers'][$key] = $value;
        } // if
        return $this;
    } // setHeader



    public function addHeader($key = '', $value = '')
    {
        if ($key == '' || $value == '') {
            return $this;
        } // if

        if (array_key_exists($key, $this->headers['headers'])) {
            // Do not change existing header key
        } else {
            $this->headers['headers'][$key] = $value;
        } // if
        return $this;
    } // addHeader



    public function setBasicAuth($username = '', $password = '')
    {
        if ($username == '' || $password == '') {
            return $this;
        } // if

        $this->headers['auth_basic'] = [$username, $password];
        return $this;
    } // setBasicAuth

    public function setBearerAuth($bearerToken = '')
    {
        if (!$bearerToken) {
            return $this;
        } // if

        $this->headers['auth_bearer'] = $bearerToken;
        return $this;
    } // setBearerAuth


    public function setBodyRawString($raw = '')
    {
        $this->headers['body'] = $raw;
        return $this;
    } // setBodyRawString

    public function setBodyArray($bodyAssocArray = [])
    {
        $this->headers['body'] = $bodyAssocArray;
        return $this;
    } // setBodyArray

    public function setTimeOut($timeout = 0)
    {
        $this->headers['timeout'] = $timeout;
        return $this;
    }

    /**
     * @param bool $verbose
     * @return HttpRequest
     */
    public function setVerbose(bool $verbose): HttpRequest
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @param bool $generateException
     * @return HttpRequest
     */
    public function setGenerateException(bool $generateException): HttpRequest
    {
        $this->generateException = $generateException;
        return $this;
    } // setTimeOut


    /**
     * Treat http response and return the corresponding data or exception
     * @param $response
     * @return array|mixed|null
     */
    private function getDataOrExceptionFromResponse($response)
    {
        if (200 !== $response->getStatusCode()) {
            // handle the HTTP request error (e.g. retry the request)
            // throw new TransportException();
            if ($this->verbose) {
                return ['status' => $response->getStatusCode(), 'message' => $response->getContent($this->generateException)];
            } // if
            return null;
        } else {
            // $response->toArray() is only available for json response
            // The content-type may have multiple values separated by ;.
            // Example: application/json; charset=utf-8
            $contentTypeString = $response->getHeaders()['content-type'][0];
            $contentTypeArray = explode(';', $contentTypeString);
            $contentTypeArray = array_map('trim', $contentTypeArray);
            if (in_array('application/json', $contentTypeArray)) {
                return $response->toArray();
            } else {
                return $response->getContent();
            } // if
        } // if
    }



    public function get($url)
    {
        try {
            if ($this->env->getEnv('ALLOWED_ORIGIN')) {
                $headers['Access-Control-Allow-Origin'] = $this->env->getEnv('ALLOWED_ORIGIN');
            } // if

            $httpClient = HttpClient::create($this->options);
            $response = $httpClient->request('GET', $url, $this->headers);
            return $this->getDataOrExceptionFromResponse($response);
        } catch (\Exception $e) {
            if ($this->verbose) {
                return ['status' => $e->getCode(), 'message' => $e->getMessage()];
            } // if
            return null;
        } // try
    } // get



    public function post($url)
    {
        try {
            $httpClient = HttpClient::create($this->options);
            $response = $httpClient->request('POST', $url, $this->headers);
            return $this->getDataOrExceptionFromResponse($response);
        } catch (\Exception $e) {
            if ($this->verbose) {
                return ['status' => $e->getCode(), 'message' => $e->getMessage()];
            } // if
            return null;
        } // try
    } // post


    public function put($url)
    {
        try {
            $httpClient = HttpClient::create($this->options);
            $response = $httpClient->request('PUT', $url, $this->headers);
            return $this->getDataOrExceptionFromResponse($response);
        } catch (\Exception $e) {
            if ($this->verbose) {
                return ['status' => $e->getCode(), 'message' => $e->getMessage()];
            } // if
            return null;
        } // try
    }


    public function patch($url)
    {
        try {
            $httpClient = HttpClient::create($this->options);
            $response = $httpClient->request('PATCH', $url, $this->headers);
            return $this->getDataOrExceptionFromResponse($response);
        } catch (\Exception $e) {
            if ($this->verbose) {
                return ['status' => $e->getCode(), 'message' => $e->getMessage()];
            } // if
            return null;
        } // try
    }

    public function delete($url)
    {
        try {
            $httpClient = HttpClient::create($this->options);
            $response = $httpClient->request('DELETE', $url, $this->headers);
            return $this->getDataOrExceptionFromResponse($response);
        } catch (\Exception $e) {
            if ($this->verbose) {
                return ['status' => $e->getCode(), 'message' => $e->getMessage()];
            } // if
            return null;
        } // try
    }

}
