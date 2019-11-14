<?php

require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_discovery.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_endpoint.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_response.php');

$called = true;
require_once(dirname(__FILE__).'/SmartHome.php');

$req = file_get_contents ( 'php://input' );
$json_data = json_decode($req);

header('Content-Type: application/json');

$alexa_discovery = AlexaDiscoveryRequest::fromJSON($json_data);
if($alexa_discovery == null)
{
    echo json_encode(new AlexaDiscoveryResponse(null));
    exit();
}
else
{
    echo discovery();
}

?>
