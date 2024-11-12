<?php

namespace Middleware;


require_once '../../vendor/autoload.php';

use HTTP\Error;
use HTTP\Request;
use Middleware\Validators\Validator;

/**
 * 
 * class Middleware
 * 
 * This class has all the middleware used in the API.
 * You call use<name> and pass the parameters to add a middleware to the middleware stack.
 * You call runMiddleware to run all the middleware in the stack.
 * 
 */
class Middleware
{

    private ?Request $request = null;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }



    /**
     * 
     * Middleware
     * 
     * @return void
     * 
     * */
    public function checkAllowedMethods(array $allowedMethods): void
    {
        // We check if the method is authorized
        // If the method is not authorized, we return an error with the allowed methods to the consumer
        if (!in_array($this->request->getRequestMethod(), $allowedMethods)) {
            $error_message = "Seules les méthodes suivantes sont autorisées : " . implode(", ", $allowedMethods);
            Error::HTTP405($error_message, []);
        }
    }

    /**
     * Middleware
     * 
     * @return void
     * 
     * */
    public function checkValidJson(): void
    {
        // We check if the client data is a valid JSON
        // If the data is not a valid JSON, we throw an error with the JSON error message and send it to the client
        if (!$this->request->getIsValidJson()) {
            $error_message = $this->request->getJsonErrorMsg();
            Error::HTTP400("Données invalides " . $error_message, []);
        }
    }

    /**
     * Middleware
     * 
     * @return void
     * 
     * */
    public function checkExpectedData(Validator $validator): void
    {
        // We check if a schema is defined
        // If a schema is defined, we parse the client data with the schema
        // If the client data is invalid against the schema, we throw an error and send it to the client
        if ($validator) {
            $client_data = $this->request->getDecodedBody();
            $data_parsed = $validator->safeParse($client_data);
            if ($data_parsed->getIsValid() === false) {
                Error::HTTP400("Données invalides", $data_parsed->getErrors());
            }
        }
    }


    /**
     * Middleware
     * 
     * 
     * @return 
     */
    public function sanitizeData($config): void
    {

        $client_data = $this->request->getDecodedBody();
        $rules = $config['sanitize'];

        // Recursive function to sanitize data depending on the rules set in config and the data type
        // use keyword is used to inject parent scope variables into the function closure scope
        // the "&" before $rules and $sanitize_recursively is used to pass the variables by reference ( avoid copying the variables )
        $sanitize_recursively = function ($client_data) use (&$rules, &$sanitize_recursively) {


            switch (gettype($client_data)) {
                case 'array':
                    return array_map($sanitize_recursively, $client_data);
                    break;

                case 'string':
                    if (in_array('html', $rules))
                        return trim(strip_tags($client_data));
                    break;

                case 'integer':
                    if (in_array('integer', $rules))
                        return filter_var($client_data, FILTER_SANITIZE_NUMBER_INT);
                    break;

                case 'double': // 'double' is the type returned for floats in PHP
                    if (in_array('float', $rules))
                        return filter_var($client_data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    break;
            }

            return $client_data;
        };

        $this->request->setDecodedBody($sanitize_recursively($client_data));
    }
}
