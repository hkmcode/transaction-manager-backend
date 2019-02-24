<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Validator;
use App\BaseModel;
use App\Repositories\Repository;

class CustomerController extends Controller
{

    protected $model;

   public function __construct(Customer $customer)
   {
       // set the model
       $this->model = new Repository($customer);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = new Customer();
        $result = $customer->getAllCustomers();
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
            'name' => 'required',
            'phone' => 'required|unique:customers,phone|digits:10',
        ]);
        
        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");
        $parameters = array('name' =>$request->name,
            'phone' => $request->phone,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('customers', $parameters)) {
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
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'phone' => 'sometimes|required|unique:customers,phone,' . $id,
        ]);

        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }
        
        $status=$this->model->update($request->only($this->model->getModel()->fillable), $id);
        if($status){
            $this->setStatusCode(201);
            return $this->respondWithSuccess($status);
        }
        else{
            $this->setStatusCode(500);
            return $this->respondWithError("Internal Server Error");
        }
    }


    public function getCustomerByTerm($term)
    {
        $customer = new Customer();
        $result = $customer->getCustomerByTerm($term);
        $this->setStatusCode(200);

        return $this->respondWithSuccess($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
