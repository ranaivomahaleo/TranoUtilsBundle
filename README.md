# TranoUtilsBundle  
  
The TranoUtilsBundle contains utilities to simplify 
the construction of json responses for REST API (CORS constraints)
by setting Http Status code and Access-Control-Allow-* headers from .env file.
This bundle sets the following json response data:
- Http Status code
- Access-Control-Allow-* headers from .env file
- Returned data

See as follows an exemple of json response for the 200 http status code:

    {
        "status": 200, 
        "message": "",
        "results": "this is an ok results"
    }

The json response format is as follows:

    {
        "status": <HTTP status code>, 
        "message": <Message related to the json response>,
        "results": <Data (string data or json data)>
    }

Installation  
------------  
Install with composer using

    composer require trano/tranoutilsbundle

Usage  
-----
The bundles consists of two parts:
- Environment reader (```ABS\UtilsBundle\Util\Env```)  
- Json response (```ABS\UtilsBundle\Util\ApiJsonResponse```)

Let us consider the .env file

```
ALLOWED_ORIGIN="*"
ALLOWED_METHODS="GET,POST,PUT,DELETE"
ALLOWED_HEADERS="Authorization, Content-Type"
```

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
The route / above returns the following json data.

    {
        "status": 200, 
        "message": "",
        "results": "this is an ok results"
    }

Additionally, the following headers will be set respectivelly from the values
of ```ALLOWED_ORIGIN```, ```ALLOWED_METHODS```, ```ALLOWED_HEADERS``` from .env file

```
Access-Control-Allow-Origin="*"
Access-Control-Allow-Headers="GET,POST,PUT,DELETE"
Access-Control-Allow-Methods="Authorization, Content-Type"
```

Important: If the ALLOWED_* are not set in .env file, the corresponsing 
Access-Control-Allow-* headers will not be set.
  
License  
-------  
This bundle is under the MIT license. See the complete license [in the bundle](LICENSE).  
  
About us  
--------  
ABSUtilsBundle is an initiative of [atety][1].  
  
[1]: https://www.atety.com
