<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\BaseModel;
use App\Company;
use App\Repositories\Repository;

class SubCompanyController extends Controller
{

    protected $model;

    public function __construct(Company $company)
    {
        // set the model
        $this->model = new Repository($company);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'parent_company_id' => 'required|exists:companies,id',
            'company_name' => 'required',
            'phone' => 'required|unique:users,phone|digits:10',
        ]);
        
        if ($validator->fails()) {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");
        $parameters = array('parent_company_id' => $request->parent_company_id,
            'company_name' => $request->company_name,
            'phone' =>  $request->phone,
            'created_at' => $created,
            'updated_at' => $created,
        );

        if (BaseModel::create('companies', $parameters)) {
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
        $validator = Validator::make($request->all(), [
            'parent_company_id' => 'sometimes|required|exists:companies,id',
            'company_name' => 'sometimes|required',
            'phone' => 'sometimes|required|unique:companies,phone,' . $id,
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
