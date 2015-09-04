<?php namespace Game\Services;

use Game\Model\Match;
use Game\Model\Continent;
use Game\Model\Region;

use Exception;

/**
 * Description of MapTemplateImplementer
 *
 * @author fvo
 */
class MapTemplateImplementer {
    
    
    protected $templates = [
        "earth" => 'Game\Model\MapTemplates\EarthMapTemplate',
    ];
    
    
    public function implement($templateName, Match $match){
        
        $templateClass = $this->templates[$templateName];
        $template = new $templateClass();
        
        if(! $template instanceof \Game\Model\MapTemplates\MapTemplate){
            throw new Exception("Cannot implement $templateClass: not an instance of \Game\Model\MapTemplates\MapTemplate");
        }
        
        $continents = $this->implementContinents($template->continentData(), $match);
        
        $regions = $this->implementRegions($template->regionData(), $continents);
        
        $this->assignNeighbors($template->regionData(), $regions);
    }
    
    
    protected function implementContinents($continentsData, $match) {
        
        $continents = [];
        
        foreach($continentsData as $index => $continentData){
            
            $continent = new Continent();
            $continent->name = $index;
            $continent->colorscheme = $continentData["colorscheme"];
            
            $continent->match()->associate($match);
            
            $continent->save();
            
            $continent->load('match');
            $continents[$index] = $continent;
            
        }
        
        return $continents;
        
    }
    
    
    protected function implementRegions($regionsData, $continentsArray) {
        
        $regions = [];
        
        foreach($regionsData as $index => $regionData){
            
            $continentName = $regionData["continent"];
            $continent = $continentsArray[$continentName];
            
            $region = new Region();
            $region->name = $index;
            $region->svgdata = $regionData["svgdata"];
            $region->centerx = $regionData["center"][0];
            $region->centery = $regionData["center"][1];
            $region->labelcenterx = $regionData["labelcenter"][0];
            $region->labelcentery = $regionData["labelcenter"][1];
            $region->angle = $regionData["angle"];
            if(isset($regionData["pathdata"])){
                $region->pathdata = $regionData["pathdata"];
            }
            
            $region->continent()->associate($continent);
            
            $region->save();
            
            $regions[$index] = $region;
            
        }
        
        return $regions;
        
    }
    
    
    protected function assignNeighbors($regionsData, $regionsArray) {
        
        foreach( $regionsArray as $index => $region){
            
            $neighborNames = $regionsData[$index]["neighbors"];
            foreach( $neighborNames as $neighborName ){
                $neighbor = $regionsArray[$neighborName];
                $region->neighbors()->attach($neighbor);
            }
            
        }
        
    }
    
}
