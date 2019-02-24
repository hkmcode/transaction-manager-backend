<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\BaseModel;
use App\Transaction;

class TransactionTypeController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = new Transaction();
        $result = $transaction->getAllTransactionTypes();
        $this->setStatusCode(200);

        return $this->respondWithSuccess($result);
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
            'type_name' => 'required',
            'abbreviation' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");
        $parameters = array('type_name' => $request->type_name,
            'abbreviation' => $request->abbreviation,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('transaction_types', $parameters)) {
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
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
