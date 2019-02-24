<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Validator;
use App\BaseModel;
use Carbon;
use App\Repositories\Repository;

class TransactionController extends Controller
{

   protected $model;

   public function __construct(Transaction $transaction)
   {
       // set the model
       $this->model = new Repository($transaction);
   }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = new Transaction();
        $result = $transaction->getAllTransactions();

       $result_array= BaseModel::queryObjectToArray($result);

        foreach($result_array as $key => $data)
        {
            $customer['id']=$data['customer_id'];
            $customer['name']=$data['customer_name'];
            $result_array[$key]['customer']=$customer;

        }
       
        $this->setStatusCode(200);

        return $this->respondWithSuccess($result_array);
    }

     /**
     * Display a transaction by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactionByDateAndType($type)
    {
        $curret_date_time = Carbon\Carbon::now();
        $curret_date=$curret_date_time->format('Y-m-d');
        $transaction = new Transaction();
        $result = $transaction->getTransactionByDateAndType($curret_date,$type);
       
        $this->setStatusCode(200);
        return $this->respondWithSuccess($result);
    }


    /**
     * get transactions by user id
     *
     * @return \Illuminate\Http\Response
     */
     public function getAllTransactionsByUser($type,$user)
     {
         $curret_date_time = Carbon\Carbon::now();
         $curret_date=$curret_date_time->format('Y-m-d');
         $transaction = new Transaction();
         $result = $transaction->getAllTransactionsByUser($curret_date,$type,$user);
        
         $this->setStatusCode(200);
         return $this->respondWithSuccess($result);
     }


    /**
     * get uncleared transactions daily
     *
     * @return \Illuminate\Http\Response
    */
    public function getDailyUnclearedTransactions($type)
    {
        $curret_date_time = Carbon\Carbon::now();
        $curret_date=$curret_date_time->format('Y-m-d');
        $transaction = new Transaction();
        $result = $transaction->getDailyUnclearedTransactions($curret_date,$type);
    
        $this->setStatusCode(200);
        return $this->respondWithSuccess($result);
    }



    /**
     * get uncleared transactions daily by user
     *
     * @return \Illuminate\Http\Response
    */
    public function getDailyUnclearedTransactionsByUser($type,$user)
    {
        $current_date_time = Carbon\Carbon::now();
        $current_date=$current_date_time->format('Y-m-d');
        $transaction = new Transaction();
        $result = $transaction->getDailyUnclearedTransactionsByUser($current_date,$type,$user);
    
        $this->setStatusCode(200); 
        return $this->respondWithSuccess($result);
    }

    /**
     * Display all transactions by abbr
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactionsByAbbr($abbr)
    {
        $transaction = new Transaction();
        $result = $transaction->getTransactionsByAbbr($abbr);

       $result_array= BaseModel::queryObjectToArray($result);

        foreach($result_array as $key => $data)
        {
            $customer['id']=$data['customer_id'];
            $customer['name']=$data['customer_name'];
            $result_array[$key]['customer']=$customer;

        }
       
        $this->setStatusCode(200);

        return $this->respondWithSuccess($result_array);
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
            'cheque_number' => 'sometimes|required',
            'amount' => 'required',
            'due_date' => 'sometimes|required',
            'user_id' => 'required',
            //'customer_id' => 'required',
            'transaction_type' => 'required',
        ]);

        //print_r($request->all());exit;

        $customer=$request->customer;

        //echo $customer['id'];exit;

        $transaction=new Transaction();

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $transaction_type=$transaction->getTransactionTypeByAbbr($request->transaction_type);

        if($request->transaction_type == self::TRANSACTION_TYPE_CHEQUE)
        {
            $cheque_number=$request->cheque_number;
        }
        else{
            $cheque_number="";
        }

        if($request->transaction_type == self::TRANSACTION_TYPE_CHEQUE || $request->transaction_type == self::TRANSACTION_TYPE_CREDIT){
            $due_date=$request->due_date;
        }
        else{
            $due_date=NULL;
        }

        $created = date("Y-m-d H:i:s");
        $parameters = array('cheque_number' => $cheque_number,
            'Amount' => $request->amount,
            'due_date' => $due_date,
            'user_id' => $request->user_id,
            'customer_id' => $customer['id'],
            'transaction_type_id' => $transaction_type->id,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('transactions', $parameters)) {
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
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=new User();
        $transaction=new Transaction();
        $user_data=$user->getUserById($request->user_id);
        $current_transaction=$this->model->find($id);
        $transaction_type=$transaction->getTransactionTypeByAbbr($request->transaction_type);
        $customer=$request->customer;
        if(!$user_data->isAdmin){
            $request->request->add(['isEdited' => self::STATUS_TRUE]);
        }

        if((!$current_transaction->isEdited) || ($user_data->isAdmin))
        {
            $request->request->add(['customer_id' => $customer['id']]);
            $request->request->add(['transaction_type_id' => $transaction_type->id]);
            $status=$this->model->update($request->only($this->model->getModel()->fillable), $id);
            $this->setStatusCode(201);
            return $this->respondWithSuccess($status);
        }
        else{
            $this->setStatusCode(500);
            return $this->respondWithError("Already edited");
        }
       
        //return $this->model->find($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }


     /**
     * set transaction cleared
     *
     * @param  int  $id
     * @return json
     */
    public function setClear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'isCleared' => 'required',
        ]);

        if ($validator->fails())
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
       
        $transactions = new Transaction();
        $result=$transactions->setClear($request->transaction_id,$request->isCleared);
        $this->setStatusCode(200);
        return $this->respondWithSuccess('Cleared Successfully');
    }

     /**
     * get transaction counts
     *
     * @param  int  $id
     * @return json
     */
    public function getTransactionCount($user_id)
    {
        $input = array('id' =>$user_id);

        $validator = Validator::make($input, [
            'id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $user=new User();
        $get_user=$user->getUserById($user_id);

        $curret_date_time = Carbon\Carbon::now();
        $curret_date=$curret_date_time->format('Y-m-d');
        
        $transaction=new Transaction();
        $total_cheque_count=$transaction->getTotalTransactionCountByStore(self::TRANSACTION_TYPE_CHEQUE,$get_user->company_id);
        $total_cheque_count_daily=$transaction->getDailyTransactionCountByStore($curret_date,self::TRANSACTION_TYPE_CHEQUE,$get_user->company_id);
        $overall_credit_total=$transaction->getTotalCreditSumByStore(self::TRANSACTION_TYPE_CREDIT,$get_user->company_id);
        $daily_credit_total=$transaction->getDailyCreditSumByStore($curret_date,self::TRANSACTION_TYPE_CREDIT,$get_user->company_id);

        $result['total_cheque']=$total_cheque_count;
        $result['daily_total_cheque']=$total_cheque_count_daily;
        $result['total_credit']=$overall_credit_total;
        $result['daily_total_credit']=$daily_credit_total;

        $this->setStatusCode(200);
        return $this->respondWithSuccess($result);  

    }
}