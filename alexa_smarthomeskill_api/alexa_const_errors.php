<?php

class AlexaErrorTypes
{
    const ENDPOINT_UNREACHABLE = 'ENDPOINT_UNREACHABLE';
    const NO_SUCH_ENDPOINT = 'NO_SUCH_ENDPOINT';
    const INVALID_AUTHORIZATION_CREDENTIAL = 'INVALID_AUTHORIZATION_CREDENTIAL';
    const INVALID_VALUE = 'INVALID_VALUE';
    const INVALID_DIRECTIVE = 'INVALUD_DIRECTIVE';
    const INTERNAL_ERROR = 'INTERNAL_ERROR';
};

class AlexaErrorMsgs
{
    const ENDPOINT_UNREACHABLE = 'Unable to reach endpoint because it appears to be offline';
    const NO_SUCH_ENDPOINT = 'The endpoint does not exist, or no longer exists.';
    const INVALID_AUTHORIZATION_CREDENTIAL = 'The authorization credential provided by Alexa is invalid. The OAuth2 access token is not valid for the customer\'s device cloud account.';
    const INVALID_VALUE = 'Invalid command for directive.';
    const INVALID_DIRECTIVE = 'Directive not available for this Skill';
    const INTERNAL_ERROR = 'Internal error';
};

class AlexaError
{
    public $type = AlexaErrorTypes::ENDPOINT_UNREACHABLE;
    public $msg = AlexaErrorMsgs::ENDPOINT_UNREACHABLE;

    public function __construct($type)
    {
        $this->type = $type;
        $this->msg = constant('AlexaErrorMsgs::'.$type);
    }
}


?>
