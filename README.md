# TranoUtilsBundle  
  
The TranoUtilsBundle contains utilities for the following purposes:
- Extended Response with configurable headers using .env file variables
- Json responses setter for REST API with configurable CORS headers using .env file variables
- Simple syntax http query service
- Environment variable reader

## Installation  
  
Install with composer using

    composer require trano/tranoutilsbundle

## Extended Response with configurable headers

The Response headers can be set using the following instruction:

```php
use Trano\UtilsBundle\Util\ExtendedResponse;

$extendedreponse = new ExtendedResponse();
$response = new Response('test');
$responseWithHeaders = $extendedreponse->encapsulateHeaders($response);
```

The headers values can be set using the following environment variables:

```dotenv
HEADER_STRICT_TRANSPORT_SECURITY=""
HEADER_CACHE_CONTROL=""
HEADER_PRAGMA=""
HEADER_REFERRER_POLICY=""
HEADER_X_CONTENT_TYPE_OPTIONS=""
HEADER_CONTENT_SECURITY_POLICY=""
HEADER_X_FRAME_OPTIONS=""
HEADER_X_XXS_PROTECTION=""
```

By default, the following secured header (https://owasp.org/) values are send back to the client when the corresponding environment variable is not set:

```
Strict-Transport-Security: max-age=31536000
Cache-Control: no-cache, no-store, must-revalidate
Pragma: no-cache
Referrer-Policy: no-referrer
X-Content-Type-Options: nosniff
Content-Security-Policy: default-src 'self'
X-Frame-Options: deny
X-XXS-Protection: 1; mode=block
```

The map between environment variables and the security headers are

| Environment variable | Header | Default value |
| --- | --- | --- |
| HEADER_STRICT_TRANSPORT_SECURITY | Strict-Transport-Security | max-age=31536000 |
| HEADER_CACHE_CONTROL | Cache-Control | no-cache, no-store, must-revalidate |
| HEADER_PRAGMA | Pragma | no-cache |
| HEADER_REFERRER_POLICY | Referrer-Policy | no-referrer |
| HEADER_X_CONTENT_TYPE_OPTIONS | X-Content-Type-Options | nosniff |
| HEADER_CONTENT_SECURITY_POLICY | Content-Security-Policy | default-src 'self' |
| HEADER_X_FRAME_OPTIONS | X-Frame-Options | deny |
| HEADER_X_XXS_PROTECTION | X-XXS-Protection | 1; mode=block |

## JsonResponse with configurable CORS headers and security headers

### JsonResponse with default security headers

Use the following code to return a JsonResponse, code 200, with the default security headers:

```php
use Trano\UtilsBundle\Util\ApiJsonResponse;

$apijsonreponse = new ApiJsonResponse();
return $apijsonreponse->_200Ok('this is an ok results');
```

The result of the above instructions is (notice the adition of the `status`, `message` and `results` keys)
```json
{
  "status": 200,
  "message": "",
  "results": "this is an ok results"
}
```

By default, the following secured header values (https://owasp.org/)  are send back to the client:

```
Strict-Transport-Security: max-age=31536000
Cache-Control: no-cache, no-store, must-revalidate
Pragma: no-cache
Referrer-Policy: no-referrer
X-Content-Type-Options: nosniff
Content-Security-Policy: default-src 'self'
X-Frame-Options: deny
X-XXS-Protection: 1; mode=block
```
The above headers can be updated using the following environment variables
```dotenv
HEADER_STRICT_TRANSPORT_SECURITY=""
HEADER_CACHE_CONTROL=""
HEADER_PRAGMA=""
HEADER_REFERRER_POLICY=""
HEADER_X_CONTENT_TYPE_OPTIONS=""
HEADER_CONTENT_SECURITY_POLICY=""
HEADER_X_FRAME_OPTIONS=""
HEADER_X_XXS_PROTECTION=""
```

To update the CORS headers (```Access-Control.*```), use the following environment variables.

```dotenv
ALLOWED_ORIGIN="*"
ALLOWED_METHODS="GET,POST"
ALLOWED_HEADERS="Authorization, Content-Type"
```
The map between environment variables and the headers are 

| Environment variable | Header |
| --- | --- |
| ALLOWED_ORIGIN | Access-Control-Allow-Origin |
| ALLOWED_METHODS | Access-Control-Allow-Methods |
| ALLOWED_HEADERS | Access-Control-Allow-Headers |

### Custom JsonResponse

By default, `ApiJsonResponse` returns a json with `status`, `message` and `results` keys. To change this behaviour with a custom json data structure, set

```dotenv
JSON_RESPONSE_TYPE=custom
```

Thus, the following php code

```php
use Trano\UtilsBundle\Util\ApiJsonResponse;

$apijsonreponse = new ApiJsonResponse();
return $apijsonreponse->_200Ok(["data" => 'this is an ok custom results']);
```
returns

```json
{
  "data": "this is an ok custom results"
}
```

## Usage of simple Http request

The ```$httprequest``` service is an instance of ```Trano\UtilsBundle\Util\HttpRequest```

### GET query with basic Auth

To return an associative array response using GET, use the following instruction. The get instruction should be at the end.

```php
    $http_array_response = $this->httprequest
            ->addHeader('Accept', '*/*')
            ->addHeader('Content-Type', 'application/json')
            ->setBasicAuth('username', 'password')
            ->get('https://www.example.com/getservice');
```

### POST query with array data and basic Auth

To return an associative array response using POST, use the instructions below.

The $data variable is sent by default using ```application/x-www-form-urlencoded``` type.
If ```https://www.example.com/postservice``` is a Symfony controller route, ```$data['data1']``` variable 
would be get using ```$request->request->get('data1');``` instruction.

The post instruction should be at the end.
``
```php
    $data = [
        'data1' => 'data1',
        'data2' => 'data2',
    ];
    $http_array_response = $this->httprequest
        ->setBasicAuth('username', 'password')
        ->setBodyArray($data)
        ->post('https://www.example.com/postservice');
```

Notice that due to the functionalities of the symfony http request, both GET and POST simple queries 
are synchronous (those queries wait for the response).

## Usage of Environment variable reader

Let us consider that we have the following environment variable file .env

```
DATABASE_URL='mysql://aaa:bbb...'
```

To read this environment variable, use the $env service, an instance of ```Trano\UtilsBundle\Util\Env``` 
as follows

```php
$database_url = $this->env->getEnv('DATABASE_URL');
````

## Example with a Symfony controller  

Let us consider the environment variable at .env file

```
ALLOWED_ORIGIN="*"
ALLOWED_METHODS="GET,POST,PUT,DELETE"
ALLOWED_HEADERS="Authorization, Content-Type"
```

Important: If the ALLOWED_* are not set in .env file, the corresponsing 
Access-Control-Allow-* headers will not be set.

To use the environment reader (```Trano\UtilsBundle\Util\Env```) 
and the Json response (```Trano\UtilsBundle\Util\ApiJsonResponse```) in a controller, use the following script

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Trano\UtilsBundle\Util\ApiJsonResponse;
use Trano\UtilsBundle\Util\Env;

class HomeController extends AbstractController
{
    /**
     * @var ApiJsonResponse
     */
    private $apijsonresponse;


    /**
     * @var Env
     */
    private $env;

    /**
     * ControllerTrait constructor.
     * @param ApiJsonResponse $apijsonresponse
     * @param Env $env
     */
    public function __construct(ApiJsonResponse $apijsonresponse, Env $env)
    {
        $this->apijsonresponse = $apijsonresponse;
        $this->env = $env;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function index(Request $request)
    {
        // Computes DATABASE_URL from .env file
        // If DATABASE_URL does not exists in .env file, it returns '' string.
        $database_url = $this->env->getEnv('DATABASE_URL');
    
        // Return Json response with the http status 200.
        return $this->apijsonresponse->_200Ok('this is an ok results');
    } // index
}
```
The route / above reads DATABASE_URL variable environment and returns the following json data.

    {
        "status": 200, 
        "message": "",
        "results": "this is an ok results"
    }
  
License  
-------  
This bundle is under the MIT license. See the complete license [in the bundle](LICENSE).  
  
About us  
--------  
TranoUtilsBundle is an initiative of [atety][1].  
  
[1]: https://www.atety.com
