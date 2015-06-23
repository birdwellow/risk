<?php namespace Game\Managers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Game\Exceptions\GameException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Exception;

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
                $user->save();

                if(File::exists($path."/".$oldFile)){
                    try{
                        File::delete($path."/".$oldFile);
                    } catch (Exception $ex) {
                        Log::error($ex);
                    }
                }
            }
        
    }
    
    
    public function savePassword($user, $passwordInputs) {
            
            $newPassword = $passwordInputs["newpassword"];
            $validator = Validator::make(
                [
                    'password' => $newPassword
                ],
                [
                    'password' => 'required|min:4|max:20'
                ]
            );
            if($validator->fails()){
                throw new GameException(
                        "USER.PASSWORDINVALID",
                        $validator->messages()
                );
            }
            
            
            $newPasswordConfirm = $passwordInputs["newpasswordconfirm"];
            if ($newPassword !== $newPasswordConfirm) {
                throw new GameException(
                        "USER.PASSWORDCONFIRMATION.NOT.MATCHING"
                );
            }
        
            $oldPassword = $passwordInputs["oldpassword"];
            if (!Auth::attempt(['email' => $user->email, 'password' => $oldPassword])) {
                throw new GameException(
                        "USER.WRONG.OLDPASSWORD"
                );
            }
            
            $user->password = Hash::make($newPassword);
            $user->save();
    }
    
    
}
