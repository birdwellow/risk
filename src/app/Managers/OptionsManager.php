<?php namespace Game\Managers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Game\Exceptions\GameException;
use Illuminate\Support\Facades\File;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class OptionsManager {
    
    
    
    public function saveOptions($user, $optionInputs){
        
            $validator = Validator::make(
                [
                    'username' => $optionInputs['username'],
                    'email' => $optionInputs['email'],
                ],
                [
                    'username' => 'required|min:5',
                    'email' => 'required|email',
                ]
            );
            if($validator->fails()){
                \Illuminate\Support\Facades\Log::info($validator->messages());
                throw new GameException(
                        "USER.INVALID.OPTIONS",
                        $validator->messages()
                );
            }
        
            $user->name = $optionInputs['username'];
            $user->email = $optionInputs['email'];
            
            $user->save();
            
            
            $avatarFile = $optionInputs['avatar'];
            if($avatarFile && $avatarFile->isValid()){
                $oldFile = $user->avatarfile;
                $path = app_path() . "/../public/img/avatars";
                $storeFileName = $user->name . "_" . uniqid() . "." . $avatarFile->getClientOriginalExtension();
                $avatarFile->move($path, $storeFileName);
                $user->avatarfile = $storeFileName;

                if(File::exists($path."/".$oldFile)){
                    File::delete($path."/".$oldFile);
                }
            }
        
    }
    
    
}
