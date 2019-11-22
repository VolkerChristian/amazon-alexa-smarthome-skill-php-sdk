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
        switch($alexa_control->request_namespace())
        {
            case 'Alexa.ModeController':
                if($alexa_control->todo() =='SetMode') 
                {
                    $contextProperty = new AlexaContextProperty("Alexa.ModeController", "mode", $alexa_control->payload->mode, 500);
                    $contextProperty->instance = $alexa_control->header->instance;
                    $context = new AlexaContext();
                    $context->add_property($contextProperty);
                    $state = new AlexaAsyncResponse($context, $alexa_control->scope()->token, $alexa_control->endpoint->endpointId, $alexa_control->correlationToken());
                
                    if($alexa_control->payload->mode == 'Open')
                    {
                        switch($alexa_control->endpoint->endpointId)
                        {
                            case 'Kitchen.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t kueche_up");
                                break;
                            case 'Street.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t strasse_up");
                                break;
                            case 'Diningtable.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t esstisch_up");
                                break;
                            case 'Balcony.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t balkon_up");
                                break;
                            case 'Sleepingroom.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t schlafzimmer_up");
                                break;
                            case 'Homeoffice.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t arbeitszimmer_up");
                                break;
                            case 'Blinds.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t komfort_up");
                                break;
                            case 'AllBlinds.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t alle_up");
                                break;
                            default:
                                $err = new AlexaError(AlexaErrorTypes::NO_SUCH_ENDPOINT);
                                $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                                break;
                        }
                    }
                    else if($alexa_control->payload->mode == 'Close')
                    {
                        switch($alexa_control->endpoint->endpointId)
                        {
                            case 'Kitchen.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t kueche_down");
                                break;
                            case 'Street.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t strasse_down");
                                break;
                            case 'Diningtable.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t esstisch_down");
                                break;
                            case 'Balcony.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t balkon_down");
                                break;
                            case 'Sleepingroom.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t schlafzimmer_down");
                                break;
                            case 'Homeoffice.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t arbeitszimmer_down");
                                break;
                            case 'Blinds.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t komfort_down");
                                break;
                            case 'AllBlinds.Jalousie':
                                exec("/usr/bin/ssh pi@werama aircontrol -t alle_down");
                                break;
                            default:
                                $err = new AlexaError(AlexaErrorTypes::NO_SUCH_ENDPOINT);
                                $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                                break;
                        }
                    }
                    else
                    {
                        $err = new AlexaError(AlexaErrorTypes::INVALID_VALUE);
                        $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                    }
                }
                break;
            default:
                $err = new AlexaError(AlexaErrorTypes::INVALID_DIRECTIVE);
                $state = new AlexaErrorResponse($alexa_control->endpoint->endpointId, $err->type, $err->msg);
                break;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($state);

?>
