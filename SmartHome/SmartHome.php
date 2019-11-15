<?php

require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_discovery.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_endpoint.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_response.php');

function jalousie($endpoint, $friendlyNames)
{
    $capabilityResources = new AlexaCapabilityResources();
    
    foreach($friendlyNames as $friendlyName){
        $capabilityResources->add_friendlyName(new AlexaFriendlyName($friendlyName));
    }
    
    $modeResourcesOpen = new AlexaModeResources();
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("öffnen"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("auf"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("aufmachen"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("hoch"));
    
    $supportedModeOpen = new AlexaSupportedMode("Open");
    $supportedModeOpen->add_modeResources($modeResourcesOpen);
    
    $modeResourcesClose = new AlexaModeResources();
    $modeResourcesClose->add_friendlyName(new AlexaFriendlyName("schließen"));
    $modeResourcesClose->add_friendlyName(new AlexaFriendlyName("zu"));
    $modeResourcesClose->add_friendlyName(new AlexaFriendlyName("zumachen"));
    $modeResourcesClose->add_friendlyName(new AlexaFriendlyName("runter"));
    
    $supportedModeClose = new AlexaSupportedMode("Close");
    $supportedModeClose->add_modeResources($modeResourcesClose);
    
    $modeConfiguration = new AlexaModeConfiguration();
    $modeConfiguration->add_supportedMode($supportedModeOpen);
    $modeConfiguration->add_supportedMode($supportedModeClose);
    
    $modeController = new AlexaCapabilityInterfaceModeController("Jalousie.".$endpoint, false, false);
    $modeController->add_capabilityResources($capabilityResources);
    $modeController->add_configuration($modeConfiguration);
    
    $iot_dev = new AlexaEndpoint($endpoint.'.Jalousie', $endpoint);
    $iot_dev->manufacturerName = "Werama";
    $iot_dev->description = "Werama Jalousien - made smart by Volker Christian";
    $iot_dev->add_displayCategories(AlexaEndpointDisplayCategories::OTHER);
    $iot_dev->add_Capability($modeController);
    $iot_dev->add_capability(new AlexaCapabilityInterfaceAlexa());
/*
    $iot_dev->add_capability(new AlexaCapabilityInterfaceEndpointHealth());
*/
/*
    $cookies = new AlexaEndpointCookies();
    $cookies->add_cookie("mykey", "this information is hidden from users.");
    $cookies->add_cookie("warning", "but dont store any confidential information here");
    $iot_dev->set_cookie($cookies);
*/
    return $iot_dev;
}

function discover()
{
    $devices = new AlexaEndpoints();
    
    $devices->add(jalousie('Kitchen', array('Küche', 'Küche Jalousie', 'Jalousie in Küche')));
    $devices->add(jalousie('Street', array('Straße', 'straßenseitige Jalousie', 'Jalousie auf Straßenseite')));
    $devices->add(jalousie('Diningtable', array('Esstisch', 'Esstisch Jalousie', 'Jalousie bei Esstisch')));
    $devices->add(jalousie('Balcony', array('Balkon', 'Balkon Jalousie', 'Jalousie bei Balkon')));
    $devices->add(jalousie('Sleepingroom', array('Schlafzimmer', 'Schlafzimmer Jalousie', 'Jalousie im Schlafzimmer')));
    $devices->add(jalousie('Homeoffice', array('Arbeitszimmer', 'Arbeitszimmer Jalousie', 'Jalousie im Arbeitszimmer')));

    $devices_response = new AlexaDiscoveryResponse($devices);

    echo json_encode($devices_response);
}

if (!isset ($called))
{
    echo discover();
}


?>
