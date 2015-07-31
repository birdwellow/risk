<?php namespace Game\Managers;

use Illuminate\Support\Facades\File;

use Exception;

/**
 * Description of FileManager
 *
 * @author fvo
 */
class FileManager {

    
        protected $avatarStorePath;
        

        public function __construct(){

                $this->avatarStorePath = app_path() . "/../public/img/avatars";

        }
        
        
        public function deleteAvatarFile($fileName) {
            
                $filePath = $this->avatarStorePath."/".$fileName;

                if(File::exists($filePath)){
                    try{
                        File::delete($filePath);
                    } catch (Exception $ex) {
                        Log::error("Unable to delete file $fileName in " . $this->avatarStorePath);
                        Log::error($e);
                    }
                }
            
        }
        
        
        public function saveAvatarFileAs($avatarFile, $storeFileName) {
            
                $avatarFile->move($this->avatarStorePath, $storeFileName);
            
        }
    
}
