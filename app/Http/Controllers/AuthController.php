<?php

namespace App\Http\Controllers;

use App\BaseModel;
use App\Http\Controllers\Controller;
use App\User;
use App\UserType;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use JWTAuth;
use Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use App\Files;

class AuthController extends Controller
{
/**
 * User Registeration
 *
 * @param Request $request
 * @return json
 */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|unique:users,phone|digits:10',
            'password' => 'required|string|min:6|max:12',
            'company_id' => 'required|exists:companies,id'
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
        $created = date("Y-m-d H:i:s");
        $user_parameters = array(
            'name' => $request->name,
            'phone' => $request->phone,
            'company_id'=>$request->company_id,
            'password' => Hash::make($request->password),
            'isEmployee' => self::VERIFIED,
            'created_at' => $created,
            'updated_at' => $created,
        );

        $user_id=BaseModel::createGetId('users', $user_parameters);
        $result['user_id']=$user_id;

        if($user_id) {
            $this->setStatusCode(201);
            return $this->respondWithSuccess($result);
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }

/**
 * Store a newly created guest resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
    public function guestRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'user_type_id' => 'required|numeric|exists:user_type,id',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:6|max:10',            
            'food_preference' =>'required',
            'food_allergies' => 'max:255',
            'avoidable_foods' => 'max:255',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");
        $user_parameters = array('first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type_id' => $request->user_type_id,
            //'region_id' => 2,
            //'designation_id' => 2,
            'email' => $request->email,
            'phone' => $request->phone,
            'profile_image_id' => '1',
            'password' => Hash::make($request->password),
            'password_bak' =>$request->password,
            'food_preference' => $request->food_preference,
            'food_allergies' => $request->food_allergies,
            'avoidable_foods' => $request->avoidable_foods,
            'isNotEmployee' => self::STATUS_TRUE,
            'isVerified' => self::VERIFIED,
            'created_at' => $created,
            'updated_at' => $created,
        );
        $user_id=BaseModel::createGetId('users', $user_parameters);
        $result['user_id']=$user_id;

        if($user_id) {
            $this->setStatusCode(201);
            return $this->respondWithSuccess($result);
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }

/**
 * API Login, on success return JWT Auth token
 *
 * @param Request $request
 * @return json
 */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //'email' => 'sometimes|required|email',
            'phone' => 'required',
            'password' => 'required',
           
        ]);
        $user = new User();
        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        // if (array_key_exists(self::LOGIN_KEY, $this->loginCredentials($request))) {
        //     $user_details = $user->getUserByEmail($request->login);
        // } else {
            $user_details = $user->getUserByPhone($request->phone);
        //}
        if ($user_details) {

            if ($token = JWTAuth::attempt($request->all())) {
                $user_data['user_id'] = $user_details->id;
                $user_data['phone'] = $user_details->phone;
                $user_data['company_id'] = $user_details->company_id;
                $user_data['isAdmin'] = $user_details->isAdmin;
                $user_data['token'] = $token;

                $this->setStatusCode(200);
                return $this->respondWithSuccess($user_data);
            } 
            else {
                $this->setStatusCode(500);
                return $this->respondWithError(array('You entered a wrong password.Please double-check and try again'));
            }
        } 
        else {
            $this->setStatusCode(500);
            return $this->respondWithError(array('The Login details you entered did not match our records.Please double check and try again'));
        }
    }

/**
 * Log out
 * Invalidate the token, so user cannot use it anymore
 * They have to relogin to get a new token
 *
 * @param Request $request
 */
    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message' => "You have successfully logged out."]);
        } catch (JWTException $e) {
        // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

/**
 * Reset Password
 *
 * @param Request $request
 */

    public function changePassword(Request $request)
    {
        if (!(Hash::check($request->get('current_password'), User::find($request->get('id'))->password))) {

            $this->setStatusCode(400);
            return $this->respondWithError(array("Your current password does not matches with the password you provided. Please try again."));

        }
        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {

            $this->setStatusCode(400);
            return $this->respondWithError(array("New Password cannot be same as your current password. Please choose a different password."));
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
        $user = User::find($request->get('id'));
        $user->password = bcrypt($request->get('new_password'));
        if ($user->save()) {
            $this->setStatusCode(200);
            return $this->respondWithSuccess('Password changed successfully');
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError(array('Internal Server Error'));
        }
    }

/**
 * Forgot passsword send email
 *
 * @param  int  $id
 * @return json
 */
    public function forgotPasswordEmail($email)
    {
        $input = array('email' => $email);
        $validator = Validator::make($input, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            $token = app('auth.password.broker')->createToken($user);
            $url = env('CLIENT_HOST_URl') . "/#/reset/" . $token;

            $mail_status = Mail::send('vendor.mail.html.reset_password', ['user' => $user, 'token' => $token, 'url' => $url], function ($m) use ($user) {
                $m->from('no-reply@solidaridadssea.org', 'Solidaridad Asia');
                $m->to($user->email)->subject('Change Password - 10 year Celebration App ');
            });
            $this->setStatusCode(201);
            return $this->respondWithSuccess('Email Successfully sent');
        } else {
            $this->setStatusCode(201);
            return $this->respondWithSuccess('User not found.Please enter your registered email');
        }

    }

/**
 * Reset the given user's password.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        if ($response == Password::PASSWORD_RESET) {
            $this->setStatusCode(200);
            return $this->respondWithSuccess('Password changed successfully');
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError(array(trans($response)));
        }

    }

/**
 * Get the password reset validation rules.
 *
 * @return array
 */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ];
    }


    public function random_password( $length = 8 ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

/**
 * Get the password reset validation error messages.
 *
 * @return array
 */
    protected function validationErrorMessages()
    {
        return [];
    }

/**
 * Get the password reset credentials from the request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

/**
 * Get the Login credentials from the request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 */
    protected function loginCredentials(Request $request)
    {
        if (is_numeric($request->get('login'))) {
            return array('phone' => $request->get('login'), 'password' => $request->get('password'));
        } else {
            return array('email' => $request->get('login'), 'password' => $request->get('password'));
        }
    }

/**
 * Reset the given user's password.
 *
 * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
 * @param  string  $password
 * @return void
 */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        $this->guard()->login($user);
    }

/**
 * Get the broker to be used during password reset.
 *
 * @return \Illuminate\Contracts\Auth\PasswordBroker
 */
    public function broker()
    {
        return Password::broker();
    }

/**
 * Get the guard to be used during password reset.
 *
 * @return \Illuminate\Contracts\Auth\StatefulGuard
 */
    protected function guard()
    {
        return Auth::guard();
    }


/**
 * Get the Firebase test
 *
 * @return \Illuminate\Contracts\Auth\StatefulGuard
 */
Public function firebaseTest()
{
    $serviceAccount = ServiceAccount::fromJsonFile(base_path().'/SolidaridadCelebration-28ab6e8fe8a4.json');

    $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->create();
    
    $auth = $firebase->getAuth();
    $userProperties = [
        'email' => 'user@example8.com',
        'emailVerified' => false,
        'phoneNumber' => '+15555550180',
        'password' => 'secretPassword',
        'displayName' => 'John Doe',
        'photoUrl' => 'http://www.example.com/12345678/photo.png',
        'disabled' => false,
    ];
    

    $database=$firebase->getDatabase();
    //$reference = $database->getReference('users');print_r($reference);exit;

    $postData = [
        'id' => '1234567890',
        'name' => 'dudu123',
        'photo' => 'http://www.example.com/12345678/photo.png',
    ];

//     $database->getReference('users')
//    ->set([
//        'name' => 'sachin1',
//        'photo' => 'http://www.example.com/12345678/photo.png',
//       ]);


    $postRef = $database->getReference('users')->push($postData);

    $postKey = $postRef->getKey(); 
    echo $postKey;
    
    // Create a key for a new post
    
   // $createdUser = $auth->createUser($userProperties);
    //print_r($newPostKey);
}

/**
 * profile update
 *
 * @param  \Illuminate\Http\Request  $request
 * @return json
 */
    public function updateProfile(Request $request)
    {
        $messages = [
            'profile_image.mimes' => 'Invalid file format selected.Upload only image files(jpeg,png,jpg formats)',
            'profile_image.max' => 'File size is too large.Try to upload less than 3MB file by cropping the image',
        ];

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:users,id',
            'profile_image' => 'required|file|mimes:jpeg,png,jpg|max:3144',
        ],$messages);

        if ($validator->fails()) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");

        $originalFileName = $request->profile_image->getClientOriginalName();
        $fileName = time() . uniqid(rand()) . '.' . $request->profile_image->getClientOriginalExtension();
        $relative_path = '/images/' . $fileName;

        $files = new Files;
        $files->file_name = $originalFileName;
        $files->file_path = $relative_path;

        if($files->save())
        {
            $profile_parameters = array(
                'profile_image_id' => $files->id,
                'updated_at' => $created,
            );

            if (BaseModel::updateRecord('users', $request->id, $profile_parameters)) 
            {
                $request->profile_image->move(public_path('images'), $fileName);
                $this->setStatusCode(200);
                return $this->respondWithSuccess('Profile Updated Successfully');
            } 
            else {
                $this->setStatusCode(500);
                return $this->respondWithError('Internal server error');
            }

        }
        else 
        {
            $this->setStatusCode(500);
            return $this->respondWithError('Image upload error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $phone_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($phone_number)
    {
        $input = array('phone' =>$phone_number);

        $validator = Validator::make($input, [
            'phone' => 'required|integer|exists:users,phone'
        ]);

        if ($validator->fails()) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        } 

        $user = new User();
        $user_data = $user->getUserByPhone($phone_number);
        
        if (BaseModel::deleteRecord('users', $user_data->id)) 
        {
            $this->setStatusCode(200);
            return $this->respondWithSuccess('User Deleted Successfully');
        } 
        else 
        {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }

/**
 * profile update
 *
 * @param  \Illuminate\Http\Request  $request
 * @return json
 */
    public function updateUserProfile(Request $request)
    {
        $messages = [
            'exists' => 'Invalid User',
        ];

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:users,id',
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'food_preference' => 'required',
        ],$messages);

        if ($validator->fails()) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");

        if ($request->user_type_id) 
        {
            $profile_parameters = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_type_id' => $request->user_type_id,
                'food_preference' => $request->food_preference,
                'updated_at' => $created,
            );
        } 
        else if ($request->designation_id)
        {
            $profile_parameters = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'designation_id' => $request->designation_id,
                'food_preference' => $request->food_preference,
                'updated_at' => $created,
            );
        }

        if (BaseModel::updateRecord('users', $request->id, $profile_parameters)) 
        {
            $this->setStatusCode(200);
            return $this->respondWithSuccess('Profile Updated Successfully');
        } 
        else 
        {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }
}
