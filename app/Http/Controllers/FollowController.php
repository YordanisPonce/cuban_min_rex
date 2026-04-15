<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Mockery\Matcher\Not;

class FollowController extends Controller
{
    public function follow($user){
        try{
            $follower_id = auth()->user()->id;
            $follow_id = User::where('name', str_replace('_', ' ', $user))->first()->id;
            if($follower_id && $follow_id){
                $follow = Follow::where('follower_id', $follower_id)->where('follow_id', $follow_id)->first();
                if($follow){
                    $follow->delete();
                } else {
                    Follow::create([
                        'follow_id' => $follow_id,
                        'follower_id' => $follower_id
                    ]);
                    if(User::find($follow_id)->ntfs_prefs->new_followers){
                        NotificationController::sendFollowerNtf($follow_id, $follower_id);
                    }
                }
                return response()->json([
                    'success' => 'Proceso completado'
                ], 200);
            }
            return response()->json([
                'error' => 'Usuarios no encontrados'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar la solicitud '.$e->getMessage()
            ], 500);
        }
    }

    public function ntf($user){
        $user_id = auth()->user()->id;
        $dj_id = User::where('name', str_replace('_', ' ', $user))->first()->id;
        if($user_id && $dj_id){
            $ntf = UserNotification::where('user_id', $user_id)->where('dj_id', $dj_id)->first();
            if($ntf){
                $ntf->delete();
            } else {
                UserNotification::create([
                    'user_id' => $user_id,
                    'dj_id' => $dj_id
                ]);
            }
            return response()->json([
                'success' => 'Proceso completado'
            ], 200);
        }
        return response()->json([
            'error' => 'Usuarios no encontrados'
        ], 422);
    }
}
