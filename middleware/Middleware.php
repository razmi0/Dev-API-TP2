<?php

namespace API\Middleware;


require_once 'vendor/autoload.php';

use API\Middleware\Validators\Validator;

/**
 * 
 * class Middleware
 * 
 */
class Middleware
{

    /**
     * Middleware
     * 
     * @return array<bool, mixed, string>
     * 
     * */
    public static function checkValidJson($json): mixed
    {
        // Decode the JSON data
        $hasError = false;
        $decodedData = json_decode($json, true);
        $error_message = null;

        // Check for errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            $hasError = true;
            $error_message = json_last_error_msg();
        }

        return [$hasError, $decodedData, $error_message];
    }

    /**
     * Middleware
     * 
     * @param Validator | null $validator
     * @param array $decoded_data
     * @return array<bool, array>
     * 
     * */
    public static function checkExpectedData(array $decoded_data, Validator $validator): array
    {
        // We check if a schema is defined
        // If a schema is defined, we parse the client data with the schema
        // If the client data is invalid against the schema, we return an error
        // else we return true
        $isValid = true;
        $errors = [];
        if ($validator) {
            $isValid = $validator->safeParse($decoded_data)->getIsValid();
            if (!$isValid) {
                $errors = $validator->getErrors();
            }
        }
        return [$isValid, $errors];
    }


    /**
     * Middleware
     * 
     * 
     * @return array
     */
    public static function sanitizeData($config, $decoded_data): array
    {

        $rules = $config['sanitize'];

        // Recursive function to sanitize data depending on the rules set in config and the data type
        // use keyword is used to inject parent scope variables into the function closure scope
        // the "&" before $rules and $sanitize_recursively is used to pass the variables by reference ( avoid copying the variables )
        $sanitize_recursively = function ($decoded_data) use (&$rules, &$sanitize_recursively) {

            switch (gettype($decoded_data)) {
                case 'array':
                    return array_map($sanitize_recursively, $decoded_data);
                    break;

                case 'string':
                    if (in_array('html', $rules))
                        return trim(strip_tags($decoded_data));
                    break;

                case 'integer':
                    if (in_array('integer', $rules))
                        return filter_var($decoded_data, FILTER_SANITIZE_NUMBER_INT);
                    break;

                case 'double': // 'double' is the type returned for floats in PHP
                    if (in_array('float', $rules))
                        return filter_var($decoded_data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    break;
            }

            return $decoded_data;
        };

        // Sanitize the data
        return $sanitize_recursively($decoded_data);
    }
}
