<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * Create/Insert details to table
     *
     * @param $table_name
     * @param $parameters
     * @return query object
     */
    public static function create($table_name, $parameters)
    {
    	return DB::table($table_name)->insert($parameters);
    }

    /**
     * Update details to table
     *
     * @param $table_name
     * @param $id
     * @param $parameters
     * @return query object
     */
    public static function updateRecord($table_name, $id, $parameters)
    {
    	return DB::table($table_name)->where('id', $id)->update($parameters);
    }

    /**
     * Get details
     *
 	 * @param $table_name
     * @return query object
     */
    public static function getDetails($table_name)
    {
    	return DB::table($table_name)->get();
    }

    /**
     * Get details by id
     *
 	 * @param $table_name
 	 * @param $id
     * @return query object
     */
    public static function getDetailsById($table_name, $id)
    {
    	return DB::table($table_name)->where('id', $id)->get();
    }

    /**
     * Delete a record/details by id
     *
 	 * @param $table_name
 	 * @param $id
     * @return query object
     */
    public static function deleteRecord($table_name, $id)
    {
    	return DB::table($table_name)->where('id', $id)->delete();
    }

    /**
     * Converts query object array
     *
     * @param $query_object
     * @return array
     */
    public static function queryObjectToArray($query_object)
    {
        return collect($query_object)->map(function($x){ return (array) $x; })->toArray();
    }

    /**
     * Get an user details by email
     *
     * @param $email
     * @return query object
     */
    public static function getUserDetailsByEmail($email)
    {
        return DB::table('users')
                ->join('company', 'users.company_id', '=', 'company.id')
                ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
                ->select('users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'user_types.id as user_type_id',
                    'user_types.user_type',
                    'users.is_admin',
                    'users.is_representative',
                    'users.is_farmer',
                    'users.status',
                    'users.created_at',
                    'users.updated_at')
                ->where('users.email', $email)
                ->first();
    }

    /**
     * Get an user details by email
     *
     * @param $email
     * @return query object
     */
    public static function checkFarmerByEmail($email)
    {
        return DB::table('users')
                ->select('users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.is_farmer',
                    'users.status',
                    'users.created_at',
                    'users.updated_at')
                ->where('users.email', $email)
                ->first();
    }

    /**
     * Get an user details by phone
     *
     * @param $phone
     * @return query object
     */
    public static function getUserDetailsByPhone($phone)
    {
        return DB::table('users')
                ->join('company', 'users.company_id', '=', 'company.id')
                ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
                ->select('users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'user_types.id as user_type_id',
                    'user_types.user_type',
                    'users.is_admin',
                    'users.is_representative',
                    'users.is_farmer',
                    'users.status',
                    'users.created_at',
                    'users.updated_at')
                ->where('users.phone', $phone)
                ->first();
    }

    /**
     * Get an user details by email
     *
     * @param $phone
     * @return query object
     */
    public static function checkFarmerByPhone($phone)
    {
        return DB::table('users')
                ->select('users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.phone',
                    'users.is_farmer',
                    'users.status',
                    'users.created_at',
                    'users.updated_at')
                ->where('users.phone', $phone)
                ->first();
    }

    /**
     * Get an crop id
     *
     * @param $crop_id
     * @return query object
     */
    public static function checkCropDeletableById($crop_id)
    {
        return DB::table('certifications')
                ->select('certifications.crop_id')
                ->where('certifications.crop_id', $crop_id)
                ->first();
    }

    /**
     * Get certifications
     *
     * @return query object
     */
    public static function getCertificationsHaveQuestion(){
        return DB::table('certifications')
            ->join('company', 'certifications.company_id', '=', 'company.id')
            ->join('crop', 'certifications.crop_id', '=', 'crop.id')
            ->select('certifications.id',
                'certifications.company_id',
                'company.company_name',
                'certifications.crop_id',
                'crop.crop_name',
                'certifications.certification_name',
                'certifications.certification_year',
                'certifications.created_at',
                'certifications.updated_at')
            ->whereExists(function ($query) {
            $query->select(DB::raw(1))
            ->from('question')
            ->whereRaw('question.certification_id = certifications.id');
            })
            ->get();
    }

    /**
     * Create/Insert details to table
     *
     * @param $table_name
     * @param $parameters
     * @return query object
     */
    public static function createGetId($table_name, $parameters)
    {
        return DB::table($table_name)->insertGetId($parameters);
    }
}
