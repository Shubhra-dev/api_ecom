<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    //

    public function join(Request $request)
    {

        // return 321;


        $fields = $request->validate([
            'phone'     => 'required|string'
        ]);

        // Check phone
        // return
        $user = Customer::where('phone', $fields['phone'])->first();

        $code = rand(1111, 9999);

        if (!$user) {

            $code = rand(1111, 9999);

            $message = "Your GCPSC verification pin {$code}";

            // $this->sendSMS($fields['phone'], $message);

            Otp::updateOrCreate(
                ['phone' => $fields['phone']],
                ['code'  => $code,]
            );
        }

        return response([
            'phone'     => $fields['phone'],
            'name'      => $user->name ?? '',
            'has_user'  => (bool) $user,
            // 'code'      => $code ?? 0,
        ], 200);
    }

    public function confirm(Request $request)
    {
        $fields = $request->validate([
            'phone'     => 'required|string',
            'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);
        $otp = $this->getOtp($fields['code'], $fields['phone']);

        if (!$otp) {
            return response([
                'message'       => 'Otp expired or doesn\'t match',
                'phone'         => $fields['phone'],
                'otp_confirm'   => false,
                'user_genesis'   => $user_genesis ?? null
            ], 400);
        }

        $response = Http::get('https://api.genesisedu.info/general/find-doc', [
            'phone' => $request->phone,
        ]);
        $user_genesis =  $response->object()->data ?? NULL;




        return response([
            'message'       => 'Otp matched!',
            'phone'         => $fields['phone'],
            'otp_confirm'   => true,
            'user_genesis'   => $user_genesis ?? null
        ], 200);
    }

    public function register(Request $request)
    {
        // return
        // !Hash::check($request->password, bcrypt($request->password));
        // return  bcrypt($request->password);


        // return
        $fields = $request->validate([
            'name'      => 'required|string',
            'phone'     => 'required|string|unique:customers,phone',
            'password'  => 'required|string|confirmed|min:3',
            // 'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);

        $user = Customer::create([
            'name'      => $fields['name'],
            'phone'     => $fields['phone'],
            'password'  => bcrypt($fields['password']),
            'tyoe'      => 4
        ]);

        Otp::find($fields['phone'])->delete();


        $token = $user->createToken(Request()->ip())->plainTextToken;
        return response($user->setAndGetLoginResponse() +
            [
                'success'   => true,
                'message'   => 'Registration successfull!',
            ], 201);




        return response($user, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'phone'     => 'required|string',
            'password'  => 'required|string|min:3',
        ]);

        // Check phone

        // $reg_match =  preg_match("/^(\+88|0088|)01([123456789])([0-9]{8})$/", $fields['phone']);
        // return 
        // substr($request->phone, -11);
        
        $user = Customer::where('phone', $fields['phone'])->first();

        // Check password
        // return Hash::check($fields['password'], $user->hash_password);

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Phone or Password wrong!',
            ], 401);
        }

        return response($user->setAndGetLoginResponse(), 201);
    }

    public function forgot_password(Request $request)
    {
        $code = rand(1111, 9999);
        $body = "Your code is {$code}";
        $content= [
            'body'=> $body, 
            'subject' => "Forgot Password"
        ];

        $mail = Mail::to('shahnewaz886@gmail.com')->send(new WelcomeEmail($content));
        return response([
            "response" => $mail ? "Email send successfully" : "failed"
        ], 200);

    }

    public function phone_verification(Request $request)
    {

        // return $request;

        $reg_match =  preg_match("/^(\+88|0088|)01([123456789])([0-9]{8})$/", $request->phone);
        if ($reg_match && strlen($request->phone) >= 11) {
            $sub_phone = substr($request->phone, -11);
            $user = User::with(['participants' => function ($q) use ($request) {
                $q->where('event_id', $request->eventID);
            },
            'participants.payments'])->where('phone', $sub_phone)->first();
            $fees = [];
            $participated = (bool)($user ? !$user->participants->isEmpty() : 0);
            if($participated && !$user->participants->status ){
                $paid = $user->participants->payments->sum();
                $fees = $user->participants->selected_fees;
                
            }

            return Response([
                'user' => (bool) $user,
                // 'participated' => $participated,
                'registered' => $user->participants->status,
                // 'fees' => $fees
            ]);
            // return
            // User::where('phone', $sub_phone)->exists();
        }
    }


    // private function loginResource(Customer $user, $token): array
    // {
    //     AuthCustomerResource::withoutWrapping();

    //     return [
    //         'user'  => new AuthCustomerResource($user),
    //         'token' => $token,
    //         'tokenHash' => base64_encode($token),
    //     ];
    // }

    public function user()
    {
        $user = request()->user()->load('address.area.district.division');

        return  $this->userResorce($user);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return response([
            'message'   => 'Logged Out'
        ], 200);
    }

    public function userResorce($user)
    {
        return [
            "id"    => $user->id,
            "name"  => $user->name,
            "phone" => $user->phone,
            "address" => (string) (($user->address->address ?? '').', '.( $user->address->area->district->name ?? '') . ', ' . ($user->address->area->name ?? '').', ' .( $user->address->area->district->division->name ?? '')  ?? ''),
        ];
    }
}
