<?php

namespace App\Http\Controllers;

use App\Enums\NotificationTypeEnum;
use App\Models\File;
use App\Models\PlayList;
use App\Models\User;

class NotificationController extends Controller
{

    public function markAllAsRead(){
        $user = auth()->user();
        
        foreach ($user->notifications as $ntf) {
            $ntf->markAsRead();
        }

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    public function deleteAll(){
        $user = auth()->user();
        
        foreach ($user->notifications as $ntf) {
            $ntf->delete();
        }

        return redirect()->back()->with('success', 'Registro limpiado');
    }

    public function delete($id){
        $user = auth()->user();
        
        $ntf = $user->notifications->find($id);

        $ntf->delete();

        return redirect()->back()->with('msg', 'Ok');
    }

    public static function sendRemixNtf($userId, $remixId){
        $user = User::find($userId);

        if($user){
            $remix = File::find($remixId);
            if($remix){
                $user->notifications()->create([
                    'type' => NotificationTypeEnum::REMIXES->value,
                    'title' => 'Nuevo Remix de '.$remix->user->name,
                    'mesage' => '"'.$remix->name.'" ya está disponible para escuchar y descargar.'
                ]);
            }
        }
    }

    public static function sendPlayListNtf($userId, $playlistId){
        $user = User::find($userId);

        if($user){
            $playlist = PlayList::find($playlistId);
            if($playlist){
                $user->notifications()->create([
                    'type' => NotificationTypeEnum::REMIXES->value,
                    'title' => 'Nueva PlayList de '.$playlist->user->name,
                    'mesage' => '"'.$playlist->name.'" ya está disponible para escuchar y descargar.'
                ]);
            }
        }
    }

    public static function sendFollowerNtf($userId, $followerId){
        $user = User::find($userId);

        if($user){
            $follower = User::find($followerId);
            if($follower){
                $user->notifications()->create([
                    'type' => NotificationTypeEnum::SOCIAL->value,
                    'title' => $follower->name.' ha comenzado a seguirte.',
                    'mesage' => 'Tienes un nuevo seguidor. Revisa su perfil y sus últimos remixes.'
                ]);
            }
        }
    }

    public static function sendPromoNtf($userId, $title, $msg){
        $user = User::find($userId);

        if($user){
            $user->notifications()->create([
                'type' => NotificationTypeEnum::PROMOTIONAL->value,
                'title' => $title,
                'mesage' => $msg
            ]);
        }
    }

    public static function sendSistemNtf($userId, $title, $msg){
        $user = User::find($userId);

        if($user){
            $user->notifications()->create([
                'type' => NotificationTypeEnum::SYSTEM->value,
                'title' => $title,
                'mesage' => $msg
            ]);
        }
    }

}
