<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'parent_company_id', 'company_name', 'phone','location'
    ];


      /**
     * Get all companies
     *
     * @return query object
     */
    public function getCompanies()
    {
    	return DB::table('companies')
    			->select('companies.id',
                    'companies.company_name',
                    'companies.location',
                    'companies.phone',
                    'companies.created_at',
                    'companies.updated_at')
                ->orderBy('companies.company_name', 'asc')
                ->where('parent_company_id','!=',0)
    			->get();
    }
}
