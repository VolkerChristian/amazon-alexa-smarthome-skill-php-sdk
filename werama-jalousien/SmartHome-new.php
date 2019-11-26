<?php
    require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_discovery.php');
    require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_endpoint.php');
    require_once(dirname(dirname(__FILE__)).'/alexa_smarthomeskill_api/alexa_response.php');

    function discover()
    {
        $fp = fopen('jalousien.json', 'r');
        $jalousien = fread($fp, filesize('jalousien.json'));
        fclose($fp);
        
        $json = json_decode($jalousien);
        
        $devices = new AlexaEndpoints();
        $devices_array = get_object_vars($json->jalousien);
        foreach($devices_array as $endpoint => $endpoint_value) {
            
            $capabilityResources = new AlexaCapabilityResources();
            foreach($endpoint_value->friendlyNames as $friendlyName) {
                $capabilityResources->add_friendlyName(new AlexaFriendlyName($friendlyName));
            }
            
            $modeConfiguration = new AlexaModeConfiguration();
            $modes_array = get_object_vars($endpoint_value->SetMode);
            foreach($modes_array as $mode => $mode_vars) {
                
                $modeResources = new AlexaModeResources();
                foreach($mode_vars->friendlyNames as $friendlyName) {
                    $modeResources->add_friendlyName(new AlexaFriendlyName($friendlyName));
                }
                $supportedMode = new AlexaSupportedMode($mode);
                $supportedMode->add_modeResources($modeResources);
                $modeConfiguration->add_supportedMode($supportedMode);
            }
            
            $modeController = new AlexaCapabilityInterfaceModeController("Jalousie.".$endpoint, false, false);
            $modeController->add_capabilityResources($capabilityResources);
            $modeController->add_configuration($modeConfiguration);
            
            $iot_dev = new AlexaEndpoint($endpoint, $endpoint_value->friendlyName);
            $iot_dev->manufacturerName = "Warema";
            $iot_dev->description = "Warema Jalousien - made smart by Volker Christian";
            $iot_dev->add_displayCategories(AlexaEndpointDisplayCategories::EXTERIOR_BLIND);
            $iot_dev->add_capability($modeController);
            $iot_dev->add_capability(new AlexaCapabilityInterfaceAlexa());
            
            $devices->add($iot_dev);
        }
        
        $devices_response = new AlexaDiscoveryResponse($devices);
        
        echo json_encode($devices_response);
        echo "\n";


    }

    if (!isset ($called))
    {
        echo discover();
    }
?>
