<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
    const VERIFIED =1;
    const NOT_VERIFIED=0;
    const LOGIN_KEY="email";
    const SESSION_DAY_FIRST="FIRST";
    const SESSION_DAY_SECOND="SECOND";
    const SESSION_TYPE_PLENARY="PLE";
    const SESSION_TYPE_BREAK="BRK";

    const TRANSACTION_TYPE_CHEQUE="CHEQ";
    CONST TRANSACTION_TYPE_CREDIT="CRED";
    CONST TRANSACTION_TYPE_COLLECTION="COLL";

     /**
     * Gets the formatted success response
     *
     * @param array $data
     * @param string $message
     * @return json
     */
    public function respondWithSuccess($data, $message = 'Success')
    {
        return $this->respond([
            'status' => $this->getStatusCode(),
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
    }

    /**
     * Gets the status code
     *
     * @return int status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code
     *
     * @param int $statusCode
     * @return object
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Gets the formatted default response
     *
     * @param array $data
     * @return json
     */
    public function respond($data, $headers = [
			'Content-Type' => 'application/json',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE, OPTIONS',
			'Access-Control-Allow-Headers' => 'Content-Type, Content-Range, Content-Disposition, Content-Description'
		])
    {
    	return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Handles an error response formatting it according to our spec.
     *
     * @param array $error
     * @param array $headers
     * @return json
     */
    protected function respondWithError($error, $headers = [])
    {
        return response()->json(['status'=>$this->getStatusCode(),'errors' => $error])->setStatusCode($this->getStatusCode());
    }

    /**
     * Returns error messages thown by validator.
     *
     * @param array $errors
     * @return json
     */
    protected function errorMessages($errors)
    {

        foreach ($errors->all() as $message)
        {
        	$error_messages[] = $message;
        }

        return $error_messages;
    }

    /**
     * Gets the formatted default response
     *
     * @param array $data
     * @return json
     */
    public function respondResultForWeb($data)
    {
    	$result = response()->json($data);
    	$data = json_decode($result->getContent(), true);
    	$datatable = array_merge(['pagination' => [], 'sort' => [], 'query' => []], $_REQUEST);

		// search filter by keywords
		$filter = isset($datatable['query']['generalSearch']) && is_string($datatable['query']['generalSearch'])
		    ? $datatable['query']['generalSearch'] : '';

		if ( ! empty($filter)) {
		    $data = array_filter($data, function ($a) use ($filter) {
		        return (boolean)preg_grep("/$filter/i", (array)$a);
		    });
		    unset($datatable['query']['generalSearch']);
		}

		// filter by field query
		$query = isset($datatable['query']) && is_array($datatable['query']) ? $datatable['query'] : null;

		if (is_array($query)) {
		    $query = array_filter($query);
		    foreach ($query as $key => $val) {
		        $data = $this->list_filter($data, [$key => $val]);
		    }
		}

		$sort  = ! empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
		$field = ! empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

		$meta    = [];
		$page    = ! empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
		$perpage = ! empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : -1;

		$pages = 1;
		$total = count($data); // total items in array

		// sort
		usort($data, function ($a, $b) use ($sort, $field) {

		    if ( ! isset($a->$field) || ! isset($b->$field)) {
		        return false;
		    }

		    if ($sort === 'asc') {
		        return $a->$field > $b->$field ? true : false;
		    }

		    return $a->$field < $b->$field ? true : false;
		});

		// $perpage 0; get all data
		if ($perpage > 0) {
		    $pages  = ceil($total / $perpage); // calculate total pages
		    $page   = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
		    $page   = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
		    $offset = ($page - 1) * $perpage;

		    if ($offset < 0) {
		        $offset = 0;
		    }

		    $data = array_slice($data, $offset, $perpage, true);
		}

		$meta = [
		    'page'    => $page,
		    'pages'   => $pages,
		    'perpage' => $perpage,
		    'total'   => $total,
		];


		// if selected all records enabled, provide all the ids
		if (isset($datatable['requestIds']) && filter_var($datatable['requestIds'], FILTER_VALIDATE_BOOLEAN)) {
		    $meta['rowIds'] = array_map(function ($row) {
		        return $row->userid;
		    }, $alldata);
		}


		$result = [
		    'meta' => $meta + [
		            'sort'  => $sort,
		            'field' => $field,
		        ],
		    'data' => $data,
		];

		$headers = [
			'Content-Type' => 'application/json',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE, OPTIONS',
			'Access-Control-Allow-Headers' => 'Content-Type, Content-Range, Content-Disposition, Content-Description'
		];

		return response()->json($result, $this->getStatusCode(), $headers);
    }

    public function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     }
}
