<?php
use Illuminate\Support\Facades\Mail;

function generateRandomNumber($length = 10) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendVerifyEmail($code, $email){
    $details = array(
        'code'=> $code,
        'title'=>'Código OTP Enviar correo'
    );
    Mail::to($email)->send(new \App\Mail\VerifyEmail($details));
}

function sendOrderVerifyEmail($code, $email){
    $details = array(
        'code'=> $code,
        'title'=>'Verificación por correo'
    );
    Mail::to($email)->send(new \App\Mail\OrderVerifyEmail($details));
}

function cmp($a, $b)
{
    return strcmp($b["ordered_count"], $a["ordered_count"]);
}

function checkStatus($user){
    $status = false;
    if($user -> status == "active"){
        switch ($user->role){
            case 'admin':
                $status = true;
                break;
            case 'restaurant':
                //check if this user is a owner
                $result = \App\Models\Restaurant::where('owner_id', $user->id)->first();
                if ($result)
                    $status = true;
                else{
                    $manage = \App\Models\RestaurantManger::where('user_id', $user->id)->first();
                    if($manage){
                        $restaurant = \App\Models\Restaurant::find($manage->restaurant_id);
                        $owner = \App\Models\User::find($restaurant->owner_id);
                        if ($owner->status == "active")
                            $status = true;
                    }
                }
                break;
            case 'waiter':
                $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
                $owner = \App\Models\User::find($restaurant->owner_id);
                if ($owner->status == "active")
                    $status = true;
                break;
            default:
                break;
        }
    }

    return $status;
}
