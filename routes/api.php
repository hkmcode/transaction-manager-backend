
<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'AuthController@register');
Route::post('/register/guest', 'AuthController@guestRegister');
Route::post('/login', 'AuthController@login');

Route::get('/forgotpassword/{email}', 'AuthController@forgotPasswordEmail')
->name('auth.forgotPasswordEmail');

//Route to create and reset password
Route::post('reset', 'AuthController@reset')
  ->name('auth.reset');

//route to delete user
Route::delete('/delete-user/{phone_number}', 'AuthController@destroy');

Route::apiResource('user-type', 'UserTypeController');
Route::get('guest-user-type', 'UserTypeController@getGuestUserTypes');

Route::apiResource('region', 'RegionController');
Route::apiResource('designation', 'DesignationController');
Route::apiResource('group-category', 'GroupCategoryController');

Route::apiResource('session-type', 'SessionTypeController');
Route::apiResource('office-location', 'OfficeLocationController');
Route::put('check-verification/{user_id}', 'UserController@updateVerification');
Route::post('store-token', 'UserController@storeDeviceToken');
Route::get('firebase', 'AuthController@firebaseTest');
Route::get('parallel', 'SessionController@getSessionByParallel');

 //route to download users
 Route::get('users-export', 'DashboardController@export');

 Route::post('update-profile', 'AuthController@updateProfile');


 Route::apiResource('transaction-type', 'TransactionTypeController');
 Route::apiResource('transaction', 'TransactionController');

//Route::group(['middleware' => ['jwt.auth']], function() {
    Route::apiResource('users', 'UserController');
    Route::apiResource('transaction', 'TransactionController');
    Route::apiResource('customer', 'CustomerController');
    Route::apiResource('company', 'CompanyController');
    Route::apiResource('subcompany', 'SubCompanyController');

    Route::get('search-customer/{term}', 'CustomerController@getCustomerByTerm');
    Route::get('transaction-abbr/{abbr}', 'TransactionController@getTransactionsByAbbr');

    Route::get('transaction-daily/{type}', 'TransactionController@getTransactionByDateAndType');
    Route::get('transaction-daily/{type}/{user}', 'TransactionController@getTransactionByUser');
    Route::get('transaction-daily-unclear/{type}', 'TransactionController@getDailyUnclearedTransactions');

    Route::get('transaction-daily-unclear-user/{type}/{user}', 'TransactionController@getDailyUnclearedTransactionsByUser');
    Route::get('transaction-count/{user_id}', 'TransactionController@getTransactionCount');

    Route::put('setclear', 'TransactionController@setclear');
    
//});
