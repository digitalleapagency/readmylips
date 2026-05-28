<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Demo\TranslationController;
use App\Http\Controllers\Demo\DemoController;
use App\Http\Controllers\Demo\AudioController;
use App\Http\Controllers\Demo\MeetingController;


use App\Http\Controllers\Api\TeamleaderController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Middleware\PreventSearchIndexing;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/join-meeting', [MeetingController::class, 'joinMeeting']);
Route::post('/twilio/webhook', [MeetingController::class, 'twilioWebhook'])->name('twilioWebhook');
Route::post('/twilio/transcription', [MeetingController::class, 'handleTranscription'])->name('handleTranscription');
Route::post('/twilio/listen', [MeetingController::class, 'twilioListen'])->name('twilioListen');

Route::post('upload-audio', [AudioController::class, 'uploadAudio']);

Route::get('/reminders_cron', [DemoController::class, 'reminders_cron']);
Route::get('/toInvoice', [DemoController::class, 'toInvoice']);

Route::get('/add_hours', [DemoController::class, 'add_hours']);

Route::get('/intro_mail', [DemoController::class, 'intro_mail']);

Route::get('/booking_converter', [DemoController::class, 'booking_converter']);

Route::get('/teamleader/auth', [TeamleaderController::class, 'redirectToTeamleader']);
Route::get('/teamleader/callback', [TeamleaderController::class, 'handleCallback']);
Route::get('/teamleader/refresh', [TeamleaderController::class, 'refreshToken']);
Route::get('/teamleader/invoice/{id}', [TeamleaderController::class, 'createInvoice']);

Route::get('/webhook/assets', [WebhookController::class, 'assets']);
Route::get('/webhook/asset/{id}/images', [WebhookController::class, 'images']);
Route::get('/webhook/asset/{id}', [WebhookController::class, 'show']);
Route::post('/webhook/asset/{id}', [WebhookController::class, 'create']);
Route::put('/webhook/asset/{id}', [WebhookController::class, 'update']);
Route::get('/webhook/asset/{id}/trigger', [WebhookController::class, 'trigger']);
Route::get('/webhook/criteria', [WebhookController::class, 'criteria']);
Route::get('/webhook/categories', [WebhookController::class, 'categories']);
Route::get('/webhook/topics', [WebhookController::class, 'topics']);

Route::get('/vectera', [DemoController::class, 'create_vectera']);
Route::get('/whereby', [DemoController::class, 'whereby']);
Route::get('/scraper', [DemoController::class, 'scraper']);

Route::get('/import_prices', [DemoController::class, 'import_prices']);
Route::get('/import_profiles', [DemoController::class, 'import_profiles']);

// Maintenance scripts that need to keep running
Route::get('/link_head_cats', [DemoController::class, 'link_head_cats']);
Route::get('/create_users', [DemoController::class, 'create_users']);

Route::get('/get_recordings', [DemoController::class, 'get_recordings']);
Route::get('/to_text_recordings', [DemoController::class, 'to_text_recordings']);
Route::get('/summarize_recordings/{meetingId?}', [DemoController::class, 'summarize_recordings']);

Route::get('/link_users_roles', [DemoController::class, 'link_users_roles']);

Route::get('/test', [DemoController::class, 'test']);
Route::get('/experts', [DemoController::class, 'experts']);
Route::post('/api/v1/expert/payment', [DemoController::class, 'mollie']);
Route::get('/expert/success/{id}', [DemoController::class, 'success']);
Route::get('/expert/{slug}', [DemoController::class, 'expert_details']);

Route::get('/auth/google', [DemoController::class, 'google_auth']);
Route::get('/auth/google/unlink', [DemoController::class, 'google_unlink']);
Route::get('/auth/google/callback', [DemoController::class, 'google_callback']);
Route::get('/auth/google/event', [DemoController::class, 'google_event']);
Route::get('/auth/google/refresh', [DemoController::class, 'google_refresh']);
Route::get('/auth/google/events', [DemoController::class, 'google_events']);
Route::get('/api/v1/auth/google/events/{date}/{timespace?}', [DemoController::class, 'google_events']);

Route::get('/auth/microsoft', [DemoController::class, 'microsoft_auth']);

Route::middleware(PreventSearchIndexing::class)->group(function () {
    Route::get('/booking/{id}/estimate', [DemoController::class, 'booking_estimate']);
    Route::get('/booking/{id}/estimate/print', [DemoController::class, 'print_estimate']);
    Route::post('/booking/{id}/estimate', [DemoController::class, 'set_estimate_status']);
    Route::get('/booking/{id}/ZXN0aW1hdGU=', [DemoController::class, 'booking_estimate']);
    Route::get('/booking/{id}/ZXN0aW1hdGU=/print', [DemoController::class, 'print_estimate']);
    Route::post('/booking/{id}/ZXN0aW1hdGU=', [DemoController::class, 'set_estimate_status']);

    Route::get('/booking/{id}/request', [DemoController::class, 'booking_request']);
    Route::post('/booking/{id}/request', [DemoController::class, 'set_booking_status']);
    Route::get('/booking/{id}/cmVxdWVzdA==/{asset?}', [DemoController::class, 'booking_request']);
    Route::get('/booking/{id}/cmVxdWVzdA==/{asset}/print', [DemoController::class, 'print_booking']);
    Route::post('/booking/{id}/cmVxdWVzdA==/{asset}', [DemoController::class, 'set_booking_status']);

    Route::get('/booking/{id}/{status}', [DemoController::class, 'set_booking_status']);
});
