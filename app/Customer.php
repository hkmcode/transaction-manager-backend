<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $guarded = [];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'name', 'phone', 'location'
    ];


    /**
     * Get all customers
     *
     * @param $user_id
     * @return query object
    */
    public function getAllCustomers()
    {
        return DB::table('customers')
                    ->select('customers.id',
                        'customers.name',
                        'customers.phone',
                        'customers.created_at',
                        'customers.updated_at')
    			    ->get();
    }

     /**
     * Get customer by term
     *
     * @param $user_id
     * @return query object
    */
    public function getCustomerByTerm($term)
    {
        return DB::table('customers')
                    ->select('customers.id',
                        'customers.name',
                        'customers.phone',
                        'customers.created_at',
                        'customers.updated_at')
                    ->where('name', 'LIKE', "%$term%")    
    			    ->get();
    }

}
