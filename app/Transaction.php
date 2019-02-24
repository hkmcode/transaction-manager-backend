<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'transaction_type_id', 'user_id', 'customer_id','cheque_number','Amount','due_date','isEdited'
    ];



    /**
     * Get all transaction types
     *
     * @param $user_id
     * @return query object
    */
    public function getAllTransactionTypes()
    {
        return DB::table('transaction_types')
                    ->select('transaction_types.id',
                        'transaction_types.type_name',
                        'transaction_types.abbreviation',
                        'transaction_types.created_at',
                        'transaction_types.updated_at')
    			->get();
    }


    /**
     * Get transaction type by abbreviation
     *
     * @param $user_id
     * @return query object
    */
    public function getTransactionTypeByAbbr($abbr)
    {
        return DB::table('transaction_types')
                    ->select('transaction_types.id',
                        'transaction_types.type_name',
                        'transaction_types.abbreviation',
                        'transaction_types.created_at',
                        'transaction_types.updated_at')
    			->where('transaction_types.abbreviation', $abbr)
    			->first();
    }

    /**
     * Get transaction type by abbreviation
     *
     * @param $user_id
     * @return query object
    */
    public function getTransactionTypeById($id)
    {
        return DB::table('transaction_types')
                    ->select('transaction_types.id',
                        'transaction_types.type_name',
                        'transaction_types.abbreviation',
                        'transaction_types.created_at',
                        'transaction_types.updated_at')
    			->where('transaction_types.id', $id)
    			->first();
    }



    /**
     * Get all transactions
     *
     * @param $user_id
     * @return query object
    */
    public function getAllTransactions()
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
    			    ->get();
    }


    /**
     * Get all transactions by abbr
     *
     * @param $user_id
     * @return query object
    */
    public function getTransactionsByAbbr($abbr)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->leftJoin('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$abbr)   
                    ->orderBY('transactions.created_at','desc') 
    			    ->get();
    }


     /**
     * Get transaction type by abbreviation
     *
     * @param $user_id
     * @return query object
    */
    public function getTransactionByDateAndType($current_date,$type)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.due_date',$current_date)     
    			    ->get();
    }


    /**
     * Get transaction type by user
     *
     * @param $user_id
     * @return query object
    */
    public function getAllTransactionsByUser($current_date,$type,$user)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.due_date',$current_date)
                    ->where('transactions.user_id',$user)     
    			    ->get();
    }


    /**
     * Get uncleared transactions
     *
     * @param $user_id
     * @return query object
    */
    public function getDailyUnclearedTransactions($current_date,$type)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.due_date',$current_date)
                    ->where('transactions.isCleared',0)     
    			    ->get();
    }




    /**
     * Get uncleared transactions
     *
     * @param $user_id
     * @return query object
    */
    public function getAllUnclearedTransactions($current_date,$type)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.isCleared',0)     
    			    ->get();
    }



    /**
     * Get uncleared transactions by user
     *
     * @param $user_id
     * @return query object
    */
    public function getDailyUnclearedTransactionsByUser($current_date,$type,$user)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('customers','transactions.customer_id','customers.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id',
                        'transactions.transaction_type_id',
                        'transactions.user_id',
                        'users.name',
                        'customers.id as customer_id',
                        'customers.name as customer_name',
                        'customers.phone as customer_phone',
                        'transactions.customer_id',
                        'transactions.cheque_number',
                        'transactions.Amount',
                        'transactions.due_date',
                        'transactions.created_at',
                        'transactions.updated_at')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.due_date',$current_date)
                    ->where('transactions.user_id',$user) 
                    ->where('transactions.isCleared',0)     
    			    ->get();
    }



    /**
     * Get count of transactions by store
     *
     * @param $user_id
     * @return query object
    */
    public function getTotalTransactionCountByStore($type,$sub_company)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('users.company_id',$sub_company)  
                    ->where('transactions.isCleared',0)        
    			    ->count();
    }
    


    /**
     * Get count of transactions daily
     *
     * @param $user_id
     * @return query object
    */
    public function getDailyTransactionCountByStore($current_date,$type,$sub_company)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->where('transactions.due_date',$current_date)
                    ->where('transaction_types.abbreviation',$type)
                    ->where('transactions.isCleared',0)     
                    ->where('users.company_id',$sub_company)     
    			    ->count();
    }


    /**
     * Get total credit sum by  store
     *
     * @param $user_id
     * @return query object
    */
    public function getTotalCreditSumByStore($type,$sub_company)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->select('transactions.id')
                    ->where('transaction_types.abbreviation',$type)
                    ->where('users.company_id',$sub_company)    
                    ->where('transactions.isCleared',0)      
    			    ->sum('transactions.Amount');
    }
    


    /**
     * Get credit sum daily
     *
     * @param $user_id
     * @return query object
    */
    public function getDailyCreditSumByStore($current_date,$type,$sub_company)
    {
        return DB::table('transactions')
                    ->join('users','transactions.user_id','users.id')
                    ->join('transaction_types','transactions.transaction_type_id','transaction_types.id')
                    ->where('transactions.due_date',$current_date)
                    ->where('transaction_types.abbreviation',$type)
                    ->where('users.company_id',$sub_company)  
                    ->where('transactions.isCleared',0)        
    			    ->sum('transactions.Amount');
    }

      /**
     * Set transaction cleared
     *
     * @param $id
     * @return query object
     */
    public static function setClear($id,$isCleared)
    {
      return DB::table('transactions')->where('id',$id)->update(['isCleared' => $isCleared]);
    }


    

    
    
}
