<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getOtp($code, $phone, $expire_time = 120)
    {
        // When the code expires, will be set null
        // return
        // Otp::where('phone', $phone)
        //     ->whereRaw('( NOW( ) - updated_at  > ' . $expire_time . ')')
        //     ->update([
        //         'code' => null
        //     ]);

        return Otp::query()
            ->select('code')
            ->where('code', $code)
            ->find($phone);
    }

    public function sendSMS($phone, $message)
    {

        $postvars = array(
            // 'userID'    => "Genesis",
            // 'passwd'    => "genesisAPI@019",
            // 'sender'    => "8801969901099",
            // 'msisdn'    => "88" . substr($phone, -11, 11),
            // 'message'   => $message,
        );

        $url = "https://vas.banglalink.net/sendSMS/sendSMS";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
