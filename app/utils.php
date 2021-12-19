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
