<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        // $response = Http::asForm()->post("http://localhost:3001/oauth/token", [
        //     "grant_type" => "authorization_code",
        //     "client_id" => "1",
        //     "client_secret" => "3k7fGdrToYOcucCrcl57AWvPRYe3DYUcnycdIkOT",
        //     "redirect_uri" => "http://localhost/api/callback",
        //     "code" => $request->code,
        // ]);

        // return $response->json();

        return $request->all();
    }
}
