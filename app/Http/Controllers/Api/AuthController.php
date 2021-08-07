<?php

namespace App\Http\Controllers\Api;

use App\Transformers\UserTransformer;
use App\Http\Controllers\Controller;
use App\Http\Traits\UploadTrait;
use App\Http\Traits\RespondTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use UploadTrait, RespondTrait;

    protected $userTransformer;

    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondNotValidated(__('app.validation.failed'), $validator->errors()->all());
        }

        $user = User::where('email', strtolower($request->input('email')))->with('role')
            ->first();

        if (empty($user)) {
            return $this->respondNotFound('', __('app.user.not_found'));
        }

        // Check password
        if (!Hash::check($request->input('password'), $user->password)) {
            return $this->respondNotAuthorized(__('app.password.not_valid'));
        }

        // Generate token
        $token = $user->createToken(config('app.name'))->accessToken;

        $transformedUser = $this->userTransformer->transform($user->toArray());

        return response()->json([
            'success' => true,
            'message' => __('app.login.success'),
            'data' => [
                'user' => $transformedUser,
                'token' => $token
            ]
        ]);
    }

    public function logout()
    {
        Auth::user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => __('app.logout.success')
        ], 200);
    }
}
