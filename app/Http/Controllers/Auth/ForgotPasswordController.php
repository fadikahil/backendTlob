<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    public function forgotPassword(Request $request) {
        ds($request->all());
        $validator = Validator::make($request->all(), [
           'email' => 'required|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        return self::sendResetLinkEmail($request);
    }

    public function showResetPassword(Request $request, $token) {

        $email = ($request->get('email'));
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function resetPasswordSuccessful() {
        return view('auth.passwords.reset-successful');
    }
}
