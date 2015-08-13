<?php namespace Game\Traits;

use Game\Services\PolicyComplianceService;

trait ChecksHttpInput {
        
        protected function check($attributesArray, $errorKey = "") {
                
                $validator = new PolicyComplianceService();
                
                $validator->check($attributesArray, $errorKey);
                
        }
        
}