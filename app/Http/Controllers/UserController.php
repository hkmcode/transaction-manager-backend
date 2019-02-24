<?php

namespace App\Http\Controllers;

use App\BaseModel;
use App\User;
use Config;
use Illuminate\Http\Request;
use PushNotification;
use Validator;
use App\Notification;
use App\Files;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
/**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
    public function index()
    {
        $user = new User();
        $user_data = $user->getUsers();
        $user_data_array = BaseModel::queryObjectToArray($user_data);
        $this->setStatusCode(200);
        return $this->respondWithSuccess($user_data);
    }

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
    public function create()
    {
//
    }

/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'office_location_id' => 'required|numeric|exists:office_location,id',
            'designation_id' => 'required|numeric|exists:designation,id',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6|max:10',
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
            'office_location_id' => $request->office_location_id,
            'designation_id' => $request->designation_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'food_preference' => $request->food_preference,
            'food_allergies' => $request->food_allergies,
            'avoidable_foods' => $request->avoidable_foods,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('users', $user_parameters)) {
            $this->setStatusCode(201);
            return $this->respondWithSuccess('Created Successfully');
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }

/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
    public function guestStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'user_type_id' => 'required|numeric|exists:user_type,id',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6|max:10',
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
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'food_preference' => $request->food_preference,
            'food_allergies' => $request->food_allergies,
            'avoidable_foods' => $request->avoidable_foods,
            'isNotEmployee' => self::STATUS_TRUE,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('users', $user_parameters)) {
            $this->setStatusCode(201);
            return $this->respondWithSuccess('Created Successfully');
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError('Internal server error');
        }
    }

/**
 * Display the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function show($id)
    {
        $user = new User();
        $user_data = $user->getUserById($id);
        $user_data_array = BaseModel::queryObjectToArray($user_data);

        foreach ($user_data_array as $key => $user_data_value) {
            if ($user_data_value["designation_name"]) {
                $user_data_array[$key]['designation_name'] = $user_data_value["designation_name"];
            } else {
                $user_detail=$user->getUserById($user_data_value['id']);
                $user_data_array[$key]['designation_name'] = $user_detail[0]->user_type_name;
            }
        }
        $this->setStatusCode(200);

        return $this->respondWithSuccess($user_data_array);
    }

/**
 * Show the form for editing the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function edit($id)
    {
//
    }

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function update(Request $request, $id)
    {
//
    }

/**
 * Checking the verification(otp) and updating the verification status of user.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function updateVerification(Request $request, $id)
    {

        $customMessages = [
            'unique' => 'Wrong Phone number.Already taken'
        ];

        $input = array('id' => $id);
        $validator = Validator::make(array_merge($input, $request->all()), [
            'id' => 'required|numeric|exists:users',
            'phone' => 'required|unique:users,phone,' . $id,
        ],$customMessages);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $update_parameters = array('phone' => $request->phone,
            'isVerified' => self::VERIFIED,
        );
        if (BaseModel::updateRecord('users', $id, $update_parameters)) {
            $this->setStatusCode(201);
            return $this->respondWithSuccess('User verification status updated');
        } else {
            $this->setStatusCode(500);
            return $this->respondWithError(array('Already Verified'));
        }
    }

/**
 * Remove the specified resource from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function destroy($id)
    {
//
    }

    /**
     * Broadcast message(push notification) from backend admin
     * 
     */

    public function sendNotificationAndroid(Request $request)
    {
        $input = array('to_users_input' => json_decode($request->to_users,true));
        $to_users=json_decode($request->to_users,true);
        $messages = [
            'to_users_input.required' => 'Select atleast one user',
            'to_users_input.*.exists' => 'Invalid User',
        ];
        $validator = Validator::make(array_merge($input,$request->all()), [
            'to_users_input' => 'required|array',
            'to_users_input.*' => 'required|exists:users,id',
            'all_users' => 'required',
            'notification_title' =>'required',
            'notification_message' =>'required'
        ],$messages);
        if ($validator->fails()) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
        $device_tokens=array();
        $user=new User();
        $notification=new Notification();
        $created = date("Y-m-d H:i:s");
        if($request->all_users)
        {
            $all_users = $user->getUsers();
            foreach ($all_users as $user_data) {
                if($user_data->device_token)
                    array_push($device_tokens, $user_data->device_token);
            }
            $notification_type=$notification->getNotificationTypeByAbbreviation(self::NOTIFICATION_TYPE_BROADCAST_ALL);
        }
        else
        {   
            
            foreach($to_users as $to_user){
                $to_user_data = $user->getUserById($to_user);
                if($to_user_data)
                    array_push($device_tokens, $to_user_data[0]->device_token);
            }
            $notification_type=$notification->getNotificationTypeByAbbreviation(self::NOTIFICATION_TYPE_BROADCAST);
        }
        $admin_user=$user->getAdminUser();
        $notification_image_id=null;
        $relative_path="";
        $notification_title=$request->notification_title;
        if($request->notification_image!="null")
        {
            $originalFileName = $request->notification_image->getClientOriginalName();
            $fileName = time() . uniqid(rand()) . '.' . $request->notification_image->getClientOriginalExtension();
            $relative_path = '/images/' . $fileName;
            $request->notification_image->move(public_path('images'), $fileName);

            $files = new Files;
            $files->file_name = $originalFileName;
            $files->file_path = $relative_path;
            if($files->save())
            {
                $notification_image_id=$files->id;
            }
            else{
                $notification_image_id=null;
            }
        }
        $notification_parameters = array('from_user_id' => $admin_user->id,
                                'notification_type_id' => $notification_type->id,
                                'notification_image_id' => $notification_image_id,
                                'notification_title' => $notification_title,
                                'notification_message' => $request->notification_message,
                                'created_at' => $created,
                                'updated_at' => $created
                                );
        $notification_save_id=BaseModel::createGetId('notification', $notification_parameters);
        $notification_details_parameters=array();
        if(!$request->all_users)
        {
            //print_r($request->notification_message);exit;
            foreach($to_users as $to_user)
            {
                $to_mail_user_data = $user->getUserById($to_user);
                $mail_status=  Mail::send('vendor.mail.html.notification_email', ['user' => $to_mail_user_data,'notification_message' => $request->notification_message], function ($m) use ($to_mail_user_data,$notification_title) {
                    $m->from('no-reply@solidaridadssea.org', 'Solidaridad Asia');
                    $m->to($to_mail_user_data[0]->email)->subject("Solidaridad Asia 10 year Celebration - ".$notification_title);
                }); 
                $notification_details_parameters[] = array('to_user_id' => $to_user, 'notification_id' => $notification_save_id,'created_at' => $created,'updated_at' => $created);
            }
            BaseModel::create('notification_details', $notification_details_parameters);
        }
        else{
            foreach($all_users as $to_user)
            {
                $to_mail_user_data = $user->getUserById($to_user->id);
                $mail_status=  Mail::send('vendor.mail.html.notification_email', ['user' => $to_mail_user_data,'notification_message' => $request->notification_message], function ($m) use ($to_mail_user_data,$notification_title) {
                    $m->from('no-reply@solidaridadssea.org', 'Solidaridad Asia');
                    $m->to($to_mail_user_data[0]->email)->subject("Solidaridad Asia 10 year Celebration - ".$notification_title);
                }); 
                //$notification_details_parameters[] = array('to_user_id' => $to_user, 'notification_id' => $notification_save_id,'created_at' => $created,'updated_at' => $created);
            }
        }
        if ($notification_save_id) 
        {      
                $status = PushNotification::setService('fcm')
                        ->setMessage([
                            'notification' => [
                                'title' => $notification_title,
                                'body' => $request->notification_message,
                                'sound' => 'default',
                            ],
                            'data' => [
                                'title' => $notification_title,
                                'message' => $request->notification_message,
                                'image' =>$relative_path,
                            ],
                        ])
                        ->setApiKey(Config::get('pushnotification.fcm.apiKey'))
                        ->setDevicesToken($device_tokens)
                        ->send()
                        ->getFeedback();
                        if($status->success){
                            $this->setStatusCode(201);
                            return $this->respondWithSuccess('Notification successfully sent');
                        }
                        else{
                            $this->setStatusCode(500);
                            return $this->respondWithError(array('Error Sending Notification..Retry'));
                        }
        }     
    }



    /**
     * Get all notifications
     * 
    */

    public function getNotificationsByUser($user_id)
    {
        $notification = new Notification();
        $all_notifications = $notification->getNotificationByUser($user_id);
        $admin_notifications=$notification->getAllAdminNotifications();
        $result_array = BaseModel::queryObjectToArray($all_notifications);
        $result_array2 = BaseModel::queryObjectToArray($admin_notifications);

        $all_notification_result=array_merge($result_array,$result_array2);
        
        foreach ($all_notification_result as $key => $data) {
            $time = strtotime($data['created_at']);
            $dt = new DateTime('@'.$time);
            $dt->setTimeZone(new DateTimeZone('Asia/Kolkata'));
            $all_notification_result[$key]['notification_time']=$dt->format('F j Y, g:i a');
            if($data['abbreviation']==self::NOTIFICATION_TYPE_BROADCAST || self::NOTIFICATION_TYPE_BROADCAST_ALL){
                $all_notification_result[$key]['from_admin']=self::STATUS_TRUE;
            }
            else{
                $all_notification_result[$key]['from_admin']=self::STATUS_FALSE;
            }
            $all_notification_result[$key]['from_image_url']='/images/153751557216228663325ba4a03472f7e.png';
        }
        $this->setStatusCode(200);
        return $this->respondWithSuccess($all_notification_result);

    }

/**
 * Update the user table with device token
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function storeDeviceToken(Request $request)
    {
        $validator = Validator::make(array_merge($request->all()), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $update_parameters = array('device_token' => $request->device_token);
        $status=BaseModel::updateRecord('users', $request->user_id, $update_parameters);
        
        $this->setStatusCode(201);
        return $this->respondWithSuccess('Device Token Updated');
    }

/**
 * Event Attendance
 *

 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function storeEventAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $user = new User();
        $check_status = $user->checkUserEventAttendanceStatus($request->user_id);
        $user_parameters = array('is_attending' => self::STATUS_TRUE);
        if ($check_status->is_attending) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError(array('You already entered the event.No need to scan again'));
        } 
        else 
        {
            if (BaseModel::updateRecord('users', $request->user_id, $user_parameters)) {
                $this->setStatusCode(201);
                return $this->respondWithSuccess('Welcome to 10 years celebration event');
            }

        }

    }

/**
 * Get event team leads.
 *
 * @return \Illuminate\Http\Response
 */
    public function getTeamLeads()
    {
        $user = new User();
        $team_leads = $user->getEventTeamLeads();
 
        $this->setStatusCode(200);
        return $this->respondWithSuccess($team_leads);
    }
}
