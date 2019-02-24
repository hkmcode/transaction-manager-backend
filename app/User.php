<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'password','company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


     /**
     * Get all users
     *
     * @return query object
     */
    public function getUsers()
    {
    	return DB::table('users')
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
    			->select('users.id',
                    'users.company_id',
                    'companies.company_name',
                    'users.name',
                    'users.phone',
                    'users.created_at',
                    'users.updated_at')
                ->orderBy('users.name', 'asc')
                ->where('isAdmin','!=',1)
    			->get();
    }

     /**
     * Get all users
     *
     * @return query object
     */
    public function getAllUsersDetails()
    {
        return DB::table('users')
                ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                ->leftJoin('user_type', 'users.user_type_id', '=', 'user_type.id')
                ->leftJoin('travel_information', 'users.id', '=', 'travel_information.user_id')
                ->leftJoin('arrival_travel_details', 'travel_information.arrival_info_id', '=', 'arrival_travel_details.id')
                ->leftJoin('departure_travel_details', 'travel_information.departure_info_id', '=', 'departure_travel_details.id')
                ->leftjoin('country','travel_information.country_id','=','country.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.food_preference',
                    'users.food_allergies',
                    'users.avoidable_foods',
                    'user_type.user_type_name',
                    'designation.designation_name',
                    'travel_information.travel_mode',
                    'arrival_travel_details.sector as arrival_sector',
                    'arrival_travel_details.airline_or_train_name as arrival_airline_or_train_name',
                    'arrival_travel_details.airline_or_train_number as arrival_airline_or_train_number',
                    'arrival_travel_details.date as arrival_date',
                    'arrival_travel_details.time as arrival_time',
                    'departure_travel_details.sector as departure_sector',
                    'departure_travel_details.airline_or_train_name as departure_airline_or_train_name',
                    'departure_travel_details.airline_or_train_number as departure_airline_or_train_number',
                    'departure_travel_details.date as departure_date',
                    'departure_travel_details.time as departure_time',
                    'travel_information.room_sharing_person_name',
                    'country.name as country_name',
                    'users.created_at',
                    'users.updated_at')
                ->orderBy('users.first_name', 'asc')
                ->where([['is_admin','!=',1],['isVerified',1]])
                ->get();
    }

     /**
     * Get all users
     *
     * @return query object
     */
    public function getNonIndianUsersDetails()
    {
        return DB::table('users')
                ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                ->leftJoin('user_type', 'users.user_type_id', '=', 'user_type.id')
                ->leftJoin('travel_information', 'users.id', '=', 'travel_information.user_id')
                ->leftJoin('arrival_travel_details', 'travel_information.arrival_info_id', '=', 'arrival_travel_details.id')
                ->leftJoin('departure_travel_details', 'travel_information.departure_info_id', '=', 'departure_travel_details.id')
                ->leftjoin('country','travel_information.country_id','=','country.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.food_preference',
                    'users.food_allergies',
                    'users.avoidable_foods',
                    'user_type.user_type_name',
                    'designation.designation_name',
                    'travel_information.travel_mode',
                    'arrival_travel_details.sector as arrival_sector',
                    'arrival_travel_details.airline_or_train_name as arrival_airline_or_train_name',
                    'arrival_travel_details.airline_or_train_number as arrival_airline_or_train_number',
                    'arrival_travel_details.date as arrival_date',
                    'arrival_travel_details.time as arrival_time',
                    'departure_travel_details.sector as departure_sector',
                    'departure_travel_details.airline_or_train_name as departure_airline_or_train_name',
                    'departure_travel_details.airline_or_train_number as departure_airline_or_train_number',
                    'departure_travel_details.date as departure_date',
                    'departure_travel_details.time as departure_time',
                    'travel_information.room_sharing_person_name',
                    'country.name as country_name',
                    'users.created_at',
                    'users.updated_at')
                ->orderBy('users.first_name', 'asc')
                ->where([['is_admin','!=',1],['isVerified',1],['country.id','!=',99]])
                ->get();
    }

     /**
     * Get all users
     *
     * @return query object
     */
    public function getIndianUsersDetails()
    {
        return DB::table('users')
                ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                ->leftJoin('user_type', 'users.user_type_id', '=', 'user_type.id')
                ->leftJoin('travel_information', 'users.id', '=', 'travel_information.user_id')
                ->leftJoin('arrival_travel_details', 'travel_information.arrival_info_id', '=', 'arrival_travel_details.id')
                ->leftJoin('departure_travel_details', 'travel_information.departure_info_id', '=', 'departure_travel_details.id')
                ->leftjoin('country','travel_information.country_id','=','country.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.food_preference',
                    'users.food_allergies',
                    'users.avoidable_foods',
                    'user_type.user_type_name',
                    'designation.designation_name',
                    'travel_information.travel_mode',
                    'arrival_travel_details.sector as arrival_sector',
                    'arrival_travel_details.airline_or_train_name as arrival_airline_or_train_name',
                    'arrival_travel_details.airline_or_train_number as arrival_airline_or_train_number',
                    'arrival_travel_details.date as arrival_date',
                    'arrival_travel_details.time as arrival_time',
                    'departure_travel_details.sector as departure_sector',
                    'departure_travel_details.airline_or_train_name as departure_airline_or_train_name',
                    'departure_travel_details.airline_or_train_number as departure_airline_or_train_number',
                    'departure_travel_details.date as departure_date',
                    'departure_travel_details.time as departure_time',
                    'travel_information.room_sharing_person_name',
                    'country.name as country_name',
                    'users.created_at',
                    'users.updated_at')
                ->orderBy('users.first_name', 'asc')
                ->where([['is_admin','!=',1],['isVerified',1],['country.id','=',99]])
                ->get();
    }

     /**
     * Get all users
     *
     * @return query object
     */
    public function getSolidaridadEmployeeUsersDetails()
    {
        return DB::table('users')
                ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                ->leftJoin('user_type', 'users.user_type_id', '=', 'user_type.id')
                ->leftJoin('travel_information', 'users.id', '=', 'travel_information.user_id')
                ->leftJoin('arrival_travel_details', 'travel_information.arrival_info_id', '=', 'arrival_travel_details.id')
                ->leftJoin('departure_travel_details', 'travel_information.departure_info_id', '=', 'departure_travel_details.id')
                ->leftjoin('country','travel_information.country_id','=','country.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.food_preference',
                    'users.food_allergies',
                    'users.avoidable_foods',
                    'user_type.user_type_name',
                    'designation.designation_name',
                    'travel_information.travel_mode',
                    'arrival_travel_details.sector as arrival_sector',
                    'arrival_travel_details.airline_or_train_name as arrival_airline_or_train_name',
                    'arrival_travel_details.airline_or_train_number as arrival_airline_or_train_number',
                    'arrival_travel_details.date as arrival_date',
                    'arrival_travel_details.time as arrival_time',
                    'departure_travel_details.sector as departure_sector',
                    'departure_travel_details.airline_or_train_name as departure_airline_or_train_name',
                    'departure_travel_details.airline_or_train_number as departure_airline_or_train_number',
                    'departure_travel_details.date as departure_date',
                    'departure_travel_details.time as departure_time',
                    'travel_information.room_sharing_person_name',
                    'country.name as country_name',
                    'users.created_at',
                    'users.updated_at')
                ->orderBy('users.first_name', 'asc')
                ->where([['is_admin','!=',1],['isVerified',1],['user_type.abbreviation','=','EMP']])
                ->get();
    }

     /**
     * Get all users
     *
     * @return query object
     */
    public function countTotalSolidaridadEmplees()
    {
        return DB::table('users')
                ->join('user_type', 'users.user_type_id', '=', 'user_type.id')
                ->select(
                    'users.id')
                ->where([['is_admin','!=',1],['user_type.abbreviation','=','EMP'],['isVerified',1]])
                ->count();
    }

     /**
     * Get user by id
     *
     * @param $user_id
     * @return query object
     */
    public function getUserById($user_id)
    {
        return DB::table('users')
                    ->select('users.id',
                        'users.company_id',
                        'users.name',
                        'users.phone',
                        'users.isAdmin',
                        'users.isEmployee',
                        'users.created_at',
                        'users.updated_at')
    			->where('users.id', $user_id)
    			->first();
    }



     /**
     * Get user by email
     *
     * @param $user_id
     * @return query object
     */
    public function getUserByEmail($email)
    {
        return DB::table('users')
                    ->leftJoin('region', '.users.region_id', '=', 'region.id')
                    ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                    ->join('user_type', 'users.user_type_id', '=', 'user_type.id')
                    ->leftJoin('files', 'users.profile_image_id', '=', 'files.id')
                    ->select('users.id',
                        'users.designation_id',
                        'users.user_type_id',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'users.phone',
                        'users.isVerified',
                        'user_type.user_type_name',
                        'designation.designation_name',
                        'region.region_name',
                        'files.file_name',
                        'files.file_path',
                        'users.created_at',
                        'users.updated_at')
                ->orderBy('users.id', 'desc')
    			->where('users.email', $email)
    			->first();
    }


    /**
     * Get user by phone
     *
     * @param $user_id
     * @return query object
     */
    public function getUserByPhone($phone)
    {
        return DB::table('users')
                    ->select('users.id',
                        'users.company_id',
                        'users.name',
                        'users.phone',
                        'users.isAdmin',
                        'users.created_at',
                        'users.updated_at')
    			->where('users.phone', $phone)
    			->first();
    }


     /**
     * Check User Event Attendance Status
     *
     * @return query object
    */
    
    public function checkUserEventAttendanceStatus($user_id)
    {
        return DB::table('users')
                ->select('users.id','users.is_attending')
    			->where('users.id',$user_id)
    			->first();
    }

     /**
     * Get event team leads users
     *
     * @return query object
     */
    public function getEventTeamLeads()
    {
        return DB::table('users')
                    ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
                    ->join('user_type', 'users.user_type_id', '=', 'user_type.id')
                    ->select('users.id',
                        'users.designation_id',
                        'users.user_type_id',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'users.phone',
                        'users.isVerified',
                        'users.food_preference',
                        'users.food_allergies',
                        'users.avoidable_foods',
                        'user_type.user_type_name',
                        'designation.designation_name',
                        'users.created_at',
                        'users.updated_at')
                ->orderBy('users.id', 'asc')
                ->where('users.is_teamlead', 1)
                ->get();
    }

    /**
    * Get Admin User
    *
    * @param $user_id
    * @return query object
    */
   public function getAdminUser()
   {
       return DB::table('users')
                   ->select('users.id',
                       'users.designation_id',
                       'users.user_type_id',
                       'users.first_name',
                       'users.last_name',
                       'users.email',
                       'users.phone',
                       'users.isVerified',
                       'users.created_at',
                       'users.updated_at')
               ->where('users.is_admin','1')
               ->first();
   }
      /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
