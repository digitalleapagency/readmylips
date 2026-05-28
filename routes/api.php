<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerUserController;
use App\Http\Controllers\Api\CustomerCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\FieldsController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\CustomerSettingController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\LoggingController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\MeetingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {	
	/* Auth routes */
	Route::post('login', [AuthenticationController::class, 'store']);
	Route::post('forgot-password', [AuthenticationController::class, 'forgot']);
	Route::post('register', [AuthenticationController::class, 'register']);
	Route::post('logout', [AuthenticationController::class, 'destroy'])->middleware('auth:api');
	
	Route::get('/assets/open', [AssetController::class, 'public']);
	
	/* Get meetings */
		Route::get('/admin/meetings/{type?}', [MeetingController::class, 'index'])->middleware('auth:api')->middleware('role:user|admin');
		Route::get('/admin/meeting/{id}', [MeetingController::class, 'show'])->middleware('auth:api')->middleware('role:user|admin');
		Route::put('/admin/meeting/{id}', [MeetingController::class, 'update'])->middleware('auth:api')->middleware('role:user|admin');
		Route::post('/admin/meetings', [MeetingController::class, 'store'])->middleware('auth:api')->middleware('role:user|admin');
		Route::get('/admin/meeting/{id}/summary', [MeetingController::class, 'mail_summary'])->middleware('auth:api')->middleware('role:user|admin');
		
		Route::get('/admin/activity/logs', [LoggingController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
  
	/* Admin routes */
		/* Get roles */
		Route::get('roles', [UserController::class, 'roles'])->middleware('auth:api')->middleware('role:admin|user');
		
		/* Create chat */
		Route::get('chats', [ChatController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('chat/unread', [ChatController::class, 'unread'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('chat/{user_id}', [ChatController::class, 'show'])->middleware('auth:api')->middleware('role:admin|user');
		Route::post('chat/{user_id}', [ChatController::class, 'message'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('chat', [ChatController::class, 'show'])->middleware('auth:api')->middleware('role:admin|user');
		Route::post('chat/twilio/retrieve', [ChatController::class, 'twilio_message']);		
		
		/* SuperAdmin:Customers */
		Route::get('/customers', [CustomerController::class, 'index']);
		Route::get('/admin/customers', [CustomerController::class, 'index'])->middleware('auth:api')->middleware('role:superadmin');
		Route::post('/admin/customers', [CustomerController::class, 'store'])->middleware('auth:api')->middleware('role:superadmin');
		Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->middleware('auth:api')->middleware('role:superadmin');
		Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->middleware('auth:api')->middleware('role:superadmin');
		
		/* Mailsettings */
		Route::get('/admin/mailsettings', [MailController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/mailsettings', [MailController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		
		/* SuperAdmin:Categories */
		Route::get('/admin/customers/{customer}/categories', [CustomerCategoryController::class, 'index'])->middleware('auth:api')->middleware('role:superadmin');
		Route::post('/admin/customers/{customer}/categories', [CustomerCategoryController::class, 'store'])->middleware('auth:api')->middleware('role:superadmin');
		
		/* SuperAdmin:Stats */
		Route::get('/admin/stats', [StatsController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/emails', [LoggingController::class, 'emails'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:Assets */
		Route::get('/admin/assets', [AssetController::class, 'admin_index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/categories', [AssetController::class, 'admin_categories'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}', [AssetController::class, 'show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/duplicate', [AssetController::class, 'duplicate'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/featured', [AssetController::class, 'toggle_featured'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/active', [AssetController::class, 'toggle_active'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/archive', [AssetController::class, 'toggle_archive'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/invite', [AssetController::class, 'invite'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/promote', [AssetController::class, 'promote'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/assets/{id}/demote', [AssetController::class, 'demote'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/assets/{id}', [AssetController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/assets/create', [AssetController::class, 'store'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:Categories */
		Route::get('/admin/assets/categories', [AssetController::class, 'admin_categories'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/categories/{id}', [CategoryController::class, 'show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/categories/{id}/active', [CategoryController::class, 'toggle_active'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/categories/{id}/archive', [CategoryController::class, 'toggle_archive'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/categories/create', [CategoryController::class, 'store'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/categories/{id}/order/{type}', [CategoryController::class, 'change_order'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:AssetFields */
		Route::get('/admin/tabs', [FieldsController::class, 'tabs_index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/tabs/{id}', [FieldsController::class, 'tabs_show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/tabs/{id}/active', [FieldsController::class, 'tabs_toggle_active'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/tabs/{id}', [FieldsController::class, 'tabs_update'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/tabs/create', [FieldsController::class, 'tabs_store'])->middleware('auth:api')->middleware('role:admin');
		
		Route::get('/admin/fields', [FieldsController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/fields/{id}', [FieldsController::class, 'show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/fields/{id}/active', [FieldsController::class, 'toggle_active'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/fields/{id}', [FieldsController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/fields/create', [FieldsController::class, 'store'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:Users */
		Route::get('/admin/users', [UserController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/user/{id}', [UserController::class, 'show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/users/{id}/active', [UserController::class, 'toggle_active'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/usertypes', [UserController::class, 'usertypes'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/users/{id}', [UserController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/users/create', [UserController::class, 'store'])->middleware('auth:api')->middleware('role:admin');
		
		/* Password */
		Route::post('/password/change', [UserController::class, 'change_password'])->middleware('auth:api')->middleware('role:admin|user');
		Route::post('/password/change/{id}', [UserController::class, 'change_password'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/logMeIn/{id}', [UserController::class, 'login_as_user'])->middleware('auth:api')->middleware('role:admin');
		
		/* Booking controller */
		Route::get('/admin/bookings/export/csv', [BookingController::class, 'exportCsv'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/bookings/{type?}', [BookingController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/unseen', [BookingController::class, 'unseen_count'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/unclaimed', [BookingController::class, 'unclaimed_count'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}', [BookingController::class, 'show'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/mail_date_request', [BookingController::class, 'mail_date_request'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/mail_estimate', [BookingController::class, 'mail_estimate_rml'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/extra_details', [BookingController::class, 'mail_extra_details_rml'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/finish', [BookingController::class, 'finish_booking'])->middleware('auth:api')->middleware('role:admin');
		Route::put('/admin/booking/{id}', [BookingController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/claim', [BookingController::class, 'claim_booking'])->middleware('auth:api')->middleware('role:admin');
		Route::get('/admin/booking/{id}/remove', [BookingController::class, 'remove'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:Voucher codes */
		Route::get('/admin/vouchers', [VoucherController::class, 'index'])->middleware('auth:api')->middleware('role:admin');
		Route::post('/admin/vouchers/create', [VoucherController::class, 'store'])->middleware('auth:api')->middleware('role:admin');
		
		/* Admin:Settings */
		Route::get('/customer/settings', [CustomerSettingController::class, 'index'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/customer/type', [CustomerSettingController::class, 'type'])->middleware('auth:api')->middleware('role:admin|user');
	
		Route::get('/admin/settings', [CustomerSettingController::class, 'index']);
		Route::put('/admin/settings', [CustomerSettingController::class, 'update'])->middleware('auth:api')->middleware('role:admin');
		
		/* Languages */
		Route::get('/assets/languages', [AssetController::class, 'languages']);
		
		/* AI Controller */
		Route::get('/eden/category/{id}/questions', [AiController::class, 'questions_for_category'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/eden/search', [AiController::class, 'search']);
		Route::get('/eden/search/{id}/thumbs/{type}', [AiController::class, 'thumbs'])->middleware('auth:api')->middleware('role:admin|user');
		
		/* User:Assets */
		Route::get('/slidervalues', [AssetController::class, 'slidervalues']);
		Route::get('/assets', [AssetController::class, 'index']);
		Route::get('/assets/fields', [AssetController::class, 'fields']);
		Route::get('/assets/categories/top', [AssetController::class, 'head_categories']);
		Route::get('/assets/categories', [AssetController::class, 'categories'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/assets/search', [AssetController::class, 'search']);
		Route::get('/assets/profile/fields', [AssetController::class, 'profile_fields'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/assets/{id}/fields', [AssetController::class, 'asset_fields']);
		Route::get('/assets/profile', [AssetController::class, 'profile'])->middleware('auth:api')->middleware('role:admin|user');
		Route::put('/assets/profile', [AssetController::class, 'update'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/assets/{id}', [AssetController::class, 'show']);
		
		/* Bookings */
		Route::get('/bookings', [BookingController::class, 'index'])->middleware('auth:api')->middleware('role:admin|user');
		Route::get('/booking/{id}', [BookingController::class, 'show'])->middleware('auth:api')->middleware('role:admin|user');
		Route::put('/booking/reason/{id}', [BookingController::class, 'update_reason']);
		
		/* Bookings */
		Route::get('/settings', [SettingController::class, 'index'])->middleware('auth:api')->middleware('role:admin|user');
		Route::put('/settings', [SettingController::class, 'update'])->middleware('auth:api')->middleware('role:admin|user');
		
		/* Check if email exists */
		Route::get('/emailcheck', [UserController::class, 'emailcheck'])->middleware('auth:api')->middleware('role:admin|user');
		
	/* Admin routes */

});
