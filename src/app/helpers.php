<?php

    function invalid( $inputName ) {
        
        $invalidFields = session("invalidfields");
        
        if($invalidFields && in_array($inputName, $invalidFields)){
            return "invalid";
        }
        
    }

    function oldordefault( $inputName, $default = "" ) {
        
        $old = old($inputName);
        if($old){
            return $old;
        } else {
            return $default;
        }
        
    }
    
    function userlabel( $user, $link = true, $small = false ) {
        
        $class = ( $small ? "user-label small" : "user-label" );
        
        $html = "";
        if($link){
            $html .= '<a href="/">';
        }
        $html .= '<div class="' . $class . '">';
        if($user->avatarfile){
            $html .= '<img class="user-avatar icon" src="/img/avatars/' . $user->avatarfile . '">';
        } else {
            $html .= '<img class="user-avatar icon" src="/img/avatars/default.png">';
        }
        $html .= '<span class="user-name">' . $user->name . '</span>';
        $html .= '</div>';
        if($link){
            $html .= '</a>';
        }
        return $html;
        
    }
    
    
    function userlabel_s( $user, $link = true ) {
        
        return userlabel( $user, $link, true );
        
    }