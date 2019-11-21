<?php

require_once(dirname(__FILE__)."/alexa_header.php");
require_once(dirname(__FILE__)."/alexa_payload.php");
require_once(dirname(__FILE__)."/alexa_response.php");

class AlexaDiscoveryRequest
{
    public $header = null;
    public $payload = null;

    public static function fromJSON(stdClass &$object)
    {
        if(!isset($object->directive))
        {
            return null;
        }
        return new AlexaDiscoveryRequest($object);
    }

    private function __construct(stdClass &$object)
    {        
        $this->header = new AlexaHeader($object->directive->header);
        $this->payload = new AlexaDiscoveryRequestPayloadScope($object->directive->payload->scope);
    }
};

class AlexaDiscoveryResponse implements JsonSerializable
{
    private $response = null;

    public function __construct($payload) 
    {
       $this->response = new AlexaResponse("Alexa.Discovery", "Discover.Response", $payload);
    }

    public function jsonSerialize() 
    {
        return $this->response;        
    }   
};



?>
