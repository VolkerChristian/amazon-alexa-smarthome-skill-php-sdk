<?php

class AlexaContextProperty implements JsonSerializable 
{
    private $namespace;
    private $name;
    private $value;
    private $timeOfSample;
    private $uncertaintyInMilliseconds;

    public function __construct($namespace, $name, $value, $uncertaintyms)
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->value = $value;
        $this->uncertaintyInMilliseconds = $uncertaintyms;
        $this->timeOfSample = date("c", time());//gmdate("Y-m-d\TH:i:s.52\Z");
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
        }
};

class AlexaContextPropertyValue implements JsonSerializable  
{
    public function __construct( $name, $value ) {$this->{$name} = $value;}
    public function jsonSerialize() {return get_object_vars($this);}
};


class AlexaContext implements JsonSerializable 
{
    private $properties = array();

    public function add_property(AlexaContextProperty $prop)
    {
        array_push($this->properties, $prop);
    }

    public function jsonSerialize() 
    {
        return [
            'properties' => $this->properties
        ];
    }
};

?>
