<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use Illuminate\Support\Str;

class MailController extends Controller {
    public function basic_email(Request $request) {
        $result = filter_var( $request->email, FILTER_VALIDATE_EMAIL );
        if ($result==false){
            $response['status'] = 1;
            $response['message'] = 'pogresan email';
            $response['code'] = 409;
            return $response;
        }

        $user = User::where('email', $request['email'])->first();
        $user1 = User::where('name', $request['name'])->first();

          if ($user) {
            $response['status'] = 2;
            $response['message'] = 'email postoji';
            $response['code'] = 409;
              return $response;
        } else if ($user1) {

            $response['status'] = 3;
            $response['message'] = 'naziv postoji';
            $response['code'] = 200;
              return $response;
        }

else {

    $random = Str::random(4) . 'BORO' . Str::random(8);

    $contactMessage = "Kod:" . $random;
    $emailTo = $request->email;
    Mail::raw($contactMessage, function ($message) use ($emailTo) {

        $message->from('pavle.cvorovic@gmail.com', 'Verifikacija');
        $message->to($emailTo);
        $message->subject('Poslato sa BalkaZone.me');

    });

    $response['status'] = 4;
    $response['message'] = 'sve je ok';
    $response['code'] = 200;
    return $response;
}
    }



}
