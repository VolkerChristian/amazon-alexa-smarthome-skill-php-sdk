<?php

require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_control.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_endpoint.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_response.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_report.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_const_errors.php');

$req = file_get_contents ( 'php://input' );
$json_data = json_decode($req);

$alexa_control = AlexaControlRequest::fromJSON($json_data);
if($alexa_control == null)
{
    //this should not happen. Maybe log error somewhere
    $err = new AlexaError(AlexaErrorTypes::INTERNAL_ERROR);
    $state = new AlexaErrorResponse("Internal Error", $err->type, $err->msg);
}
else
{
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer " . $alexa_control->scope()->token . "\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $oauth_user = file_get_contents('https://cloud.vchrist.at/ocs/v2.php/cloud/user?format=json', false, $context);
    $oauth_user_data = json_decode($oauth_user);

    $user_check_faild = $oauth_user_data->ocs->meta->message != "OK" || !in_array("Amazon", $oauth_user_data->ocs->data->groups);
    
    if($user_check_faild)
    {
        $err = new AlexaError(AlexaErrorTypes::INVALID_AUTHORIZATION_CREDENTIAL);
        $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
    }
    else
    {
        $fp = fopen('jalousien.json', 'r');
        $jalousien = fread($fp, filesize('jalousien.json'));
        fclose($fp);
        $json = json_decode($jalousien);
        
        $endpoint = $alexa_control->endpoint->endpointId;
        $alexa_control_todo = $alexa_control->todo();
        $alexa_control_payload_mode = $alexa_control->payload->mode;
        
        if ($alexa_control->request_namespace() == "Alexa.ModeController")
        {
            $contextProperty = new AlexaContextProperty("Alexa.ModeController", "mode", $alexa_control_payload_mode, 500);
            $contextProperty->instance = $alexa_control->header->instance;
            $context = new AlexaContext();
            $context->add_property($contextProperty);
            $state = new AlexaAsyncResponse($context, $alexa_control->scope()->token, $endpoint, $alexa_control->correlationToken());
            
            if (isset($json->jalousien->$endpoint))
            {
                if (isset($json->jalousien->$endpoint->$alexa_control_todo))
                {
                    if (isset($json->jalousien->$endpoint->$alexa_control_todo->$alexa_control_payload_mode))
                    {
                        $connection = ssh2_connect($json->jalousien->$endpoint->$alexa_control_todo->$alexa_control_payload_mode->host, 22);
                        ssh2_auth_pubkey_file($connection, 'pi', '/var/www/.ssh/id_rsa.pub', '/var/www/.ssh/id_rsa', '');
                        if ( $connection != FALSE ) {
                            $contextProperty = new AlexaContextProperty("Alexa.ModeController", "mode", $alexa_control_payload_mode, 500);
                            $contextProperty->instance = $alexa_control->header->instance;
                            $context = new AlexaContext();
                            $context->add_property($contextProperty);
                            $state = new AlexaAsyncResponse($context, $alexa_control->scope()->token, $endpoint, $alexa_control->correlationToken());
                            ssh2_exec($connection, $json->jalousien->$endpoint->$alexa_control_todo->$alexa_control_payload_mode->command);
                        } else {
                            $err = new AlexaError(AlexaErrorTypes::ENDPOINT_UNREACHABLE);
                            $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                        }
                        ssh2_disconnect ($connection);
                        unset($connection);
                    }
                    else
                    {
                        $err = new AlexaError(AlexaErrorTypes::INVALID_VALUE);
                        $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                    }
                }
                else
                {
                    $err = new AlexaError(AlexaErrorTypes::INVALID_DIRECTIVE);
                    $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                }
            }
            else
            {
                $err = new AlexaError(AlexaErrorTypes::NO_SUCH_ENDPOINT);
                $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
            }
        }
        else 
        {
            $err = new AlexaError(AlexaErrorTypes::INVALID_DIRECTIVE);
            $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
        }
    }
}

header('Content-Type: application/json');
echo json_encode($state);

?>
