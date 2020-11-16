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
     * @var float
     */
    private $timeout = 0;


    public function resetHeader()
    {
        $this->headers = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => '1.0'
            ]
        ];
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
    } // setTimeOut


    public function get($url)
    {
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $url, $this->headers);
            // do this instead
            if (200 !== $response->getStatusCode()) {
                // handle the HTTP request error (e.g. retry the request)
                throw new TransportException();
            } else {
                $responseBody = $response->toArray();
                return $responseBody;
            } // if
        } catch (TransportExceptionInterface $transportException) {
            return null;
        } // try
    } // get



    public function post($url)
    {
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', $url, $this->headers);
            // do this instead
            if (200 !== $response->getStatusCode()) {
                // handle the HTTP request error (e.g. retry the request)
                throw new TransportException();
            } else {
                $responseBody = $response->toArray();
                return $responseBody;
            } // if
        } catch (TransportExceptionInterface $transportException) {
            return null;
        } // try
    } // post



}
