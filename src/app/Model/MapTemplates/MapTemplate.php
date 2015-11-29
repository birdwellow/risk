<?php namespace Game\Model\MapTemplates;

abstract class MapTemplate {
    
    protected $continentsData;
    protected $regionsData;
    
    public function regionData() {
        return $this->regionsData;
    }
    
    public function continentData() {
        return $this->continentsData;
    }
    
    public function connectionData() {
        return $this->connections;
    }
    
}
