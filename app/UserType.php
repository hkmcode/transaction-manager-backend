<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserType extends Model
{
    
     /**
     * Get UserTypes
     *
     * @return query object
     */
    public function getUserTypes()
    {
    	return DB::table('user_type')
            ->select('user_type.id',
                'user_type.user_type_name',
                'user_type.abbreviation',
                'user_type.created_at',
                'user_type.updated_at')
            ->orderBy('user_type.id')
            ->get();
    }


     /**
     * Get user-type by id
     *
     * @param $user_id
     * @return query object
     */
    public function getUserTypeById($user_type_id)
    {
    	return DB::table('user_type')
    			->select('user_type.id as user_type_id',
                    'user_type.user_type_name',
                    'user_type.created_at',
    				'user_type.updated_at')
    			->where('user_type.id', $user_type_id)
    			->get();
    }

     /**
     * Get user-type by id
     *
     * @param $user_id
     * @return query object
     */
    public function getEmpUsertype()
    {
    	return DB::table('user_type')
    			->select('user_type.id as user_type_id',
                    'user_type.user_type_name',
                    'user_type.created_at',
    				'user_type.updated_at')
    			->where('user_type.abbreviation', 'EMP')
    			->first();
    }


     /**
     * Get Guest UserTypes
     *
     * @return query object
     */
    public function getGuestUserTypes()
    {
    	return DB::table('user_type')
            ->select('user_type.id',
                'user_type.user_type_name',
                'user_type.abbreviation',
                'user_type.created_at',
                'user_type.updated_at')
            ->whereRaw("abbreviation != 'EMP' and abbreviation != 'GEMP'")
            ->orderBy('user_type.id')
            ->get();
    }
}
