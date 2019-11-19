<?php

require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_discovery.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_endpoint.php');
require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_response.php');

function jalousie($endpoint, $friendlyName, $friendlyNames)
{
    $capabilityResources = new AlexaCapabilityResources();
    
    foreach($friendlyNames as $fn){
        $capabilityResources->add_friendlyName(new AlexaFriendlyName($fn));
    }
    
    $modeResourcesOpen = new AlexaModeResources();
//    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName('Alexa.Value.Open', 'asset'));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("öffnen"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("auf"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("aufmachen"));
    $modeResourcesOpen->add_friendlyName(new AlexaFriendlyName("hoch"));
    
    $supportedModeOpen = new AlexaSupportedMode("Open");
    $supportedModeOpen->add_modeResources($modeResourcesOpen);
    
    $modeResourcesClose = new AlexaModeResources();
//    $modeResourcesClose->add_friendlyName(new AlexaFriendlyName('Alexa.Value.Close', 'asset'));
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
    
    $iot_dev = new AlexaEndpoint($endpoint.'.Jalousie', $friendlyName);
    $iot_dev->manufacturerName = "Werama";
    $iot_dev->description = "Werama Jalousien - made smart by Volker Christian";
    $iot_dev->add_displayCategories(AlexaEndpointDisplayCategories::EXTERIOR_BLIND);
    $iot_dev->add_capability($modeController);
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

function comfort($endpoint, $friendlyName)
{
    $sceneController = new AlexaCapabilityInterfaceSceneController();
    $sceneController->supportsDeactivation = false;
    $sceneController->proactivelyReported = false;
    $iot_dev = new AlexaEndpoint($endpoint, $friendlyName);
    $iot_dev->manufacturerName = "Werama";
    $iot_dev->description = "Werama Jalousien - made smart by Volker Christian";
    $iot_dev->add_displayCategories(AlexaEndpointDisplayCategories::SCENE_TRIGGER);
    $iot_dev->add_capability($sceneController);
    
    return $iot_dev;
}

function discover()
{
    $devices = new AlexaEndpoints();
    
    $devices->add(jalousie('Kitchen', 'Küche', array('Küchen Jalousie', 'Küchen Jalousien', 'Jalousie in Küche')));
    $devices->add(jalousie('Street', 'Straßenseite', array('Straßenseite Jalousie', 'Straßenseite Jalousien', 'Jalousie auf Straßenseite')));
    $devices->add(jalousie('Diningtable', 'Esstisch', array('Esstisch Jalousie', 'Esstisch Jalousien', 'Jalousie bei Esstisch')));
    $devices->add(jalousie('Balcony', 'Balkon', array('Balkon Jalousie', 'Balkon Jalousien', 'Jalousie bei Balkon')));
    $devices->add(jalousie('Sleepingroom', 'Schlafzimmer', array('Schlafzimmer Jalousie', 'Schlafzimmer Jalousien', 'Jalousie im Schlafzimmer')));
    $devices->add(jalousie('Homeoffice', 'Arbeitszimmer', array('Arbeitszimmer Jalousie', 'Arbeitszimmer Jalousien', 'Jalousie im Arbeitszimmer')));
    $devices->add(jalousie('Blinds', 'Jalousien', array('Rollos')));
    $devices->add(jalousie('AllBlinds', 'alle Jalousien', array('alle Rollos')));

    /*
    $devices->add(comfort('Comfort.Close', 'Jalousien schließen'));
    $devices->add(comfort('Comfort.Open', 'Jalousien öffnen'));
    
    $devices->add(comfort('All.Close', 'Alle Jalousien schließen'));
    $devices->add(comfort('All.Open', 'Alle Jalousien öffnen'));
    */
    $devices_response = new AlexaDiscoveryResponse($devices);

    echo json_encode($devices_response);
}

if (!isset ($called))
{
    echo discover();
}


?>
