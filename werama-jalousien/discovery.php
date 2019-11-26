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
}
else
{
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer " . $alexa_discovery->payload->token . "\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $oauth_user = file_get_contents('https://cloud.vchrist.at/ocs/v2.php/cloud/user?format=json', false, $context);
    $oauth_user_data = json_decode($oauth_user);
    
    $user_check_faild = $oauth_user_data->ocs->meta->message != "OK" || !in_array("Amazon", $oauth_user_data->ocs->data->groups);
    
    if ($user_check_faild)
    {
        echo json_encode(new AlexaDiscoveryResponse(null));
    }
    else
    {
        echo discover($alexa_discovery->payload->token);
//        echo discover();
    }
}

?>
