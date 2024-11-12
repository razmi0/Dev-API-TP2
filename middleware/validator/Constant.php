<?php


namespace Middleware\Validators;


/**
 * class Constant
 * 
 * All those constants emerged from the database and are use to validate the client data
 * 
 */
class Constant
{
    public const NAME_REGEX = "/^[a-zA-Z0-9-'%,.:\/&()|; ]+$/";
    public const DESCRIPTION_REGEX = "/^[a-zA-Z0-9-'%,.:\/&()|; ]+$/";
    public const PRICE_REGEX = "/^[0-9.]+$/";
    public const ID_REGEX = "/^[0-9]+$/";
}
