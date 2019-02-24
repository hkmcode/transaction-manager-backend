<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use App\Designation;
use Validator;


class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designation = new Designation();
        $result = $designation->getDesignations();
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
            'designation_name' => 'required|max:50|unique:designation,designation_name'
        ]);

        if ($validator->fails()) 
        {
            $this->setStatusCode(400);
            return $this->respondWithError($this->errorMessages($validator->errors()));
        }

        $created = date("Y-m-d H:i:s");
        $designation_parameters = array('designation_name' => $request->designation_name,
                                        'created_at' => $created,
                                        'updated_at' => $created
                                      );
        
        if (BaseModel::create('designation', $designation_parameters)) 
        {
            $this->setStatusCode(201);
            return $this->respondWithSuccess('Created Successfully');
        } 
        else 
        {
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
