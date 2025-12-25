<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {


    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    Route::post('companies/{company}/switch', [\App\Http\Controllers\CompanyController::class, 'switch'])->name('companies.switch');

    Route::resource('providers', \App\Http\Controllers\ProviderController::class);
    Route::post('providers/{provider}/test', [\App\Http\Controllers\ProviderController::class, 'testConnection'])->name('providers.test');

    Route::resource('senders', \App\Http\Controllers\SenderController::class);
    Route::post('senders/{sender}/verify', [\App\Http\Controllers\SenderController::class, 'verify'])->name('senders.verify');

    Route::resource('contacts', \App\Http\Controllers\ContactController::class);
    Route::resource('lists', \App\Http\Controllers\ContactListController::class);
    Route::post('lists/{list}/add-contact', [\App\Http\Controllers\ContactListController::class, 'addContact'])->name('lists.add_contact');
    Route::delete('lists/{list}/remove-contact/{contact}', [\App\Http\Controllers\ContactListController::class, 'removeContact'])->name('lists.remove_contact');

    Route::post('segments/count', [\App\Http\Controllers\SegmentController::class, 'count'])->name('segments.count');
    Route::resource('segments', \App\Http\Controllers\SegmentController::class);

    Route::resource('imports', \App\Http\Controllers\ImportController::class);
    Route::post('imports/{import}/map', [\App\Http\Controllers\ImportController::class, 'map'])->name('imports.map');
    Route::post('imports/{import}/process', [\App\Http\Controllers\ImportController::class, 'process'])->name('imports.process');

    Route::resource('templates', \App\Http\Controllers\TemplateController::class);
    Route::resource('automations', \App\Http\Controllers\AutomationController::class);
    Route::resource('campaigns', \App\Http\Controllers\CampaignController::class);
    Route::post('campaigns/{campaign}/launch', [\App\Http\Controllers\CampaignController::class, 'launch'])->name('campaigns.launch');
    Route::post('campaigns/{campaign}/cancel-schedule', [\App\Http\Controllers\CampaignController::class, 'cancelSchedule'])->name('campaigns.cancel_schedule');
    Route::post('campaigns/{campaign}/pause', [\App\Http\Controllers\CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('campaigns/{campaign}/resume', [\App\Http\Controllers\CampaignController::class, 'resume'])->name('campaigns.resume');
    Route::post('campaigns/{campaign}/stop', [\App\Http\Controllers\CampaignController::class, 'stop'])->name('campaigns.stop');
    Route::post('campaigns/{campaign}/duplicate', [\App\Http\Controllers\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
    Route::get('campaigns/{campaign}/export', [\App\Http\Controllers\CampaignController::class, 'export'])->name('campaigns.export');

    Route::get('queue-monitor', [\App\Http\Controllers\QueueMonitorController::class, 'index'])->name('queue.monitor');

    Route::resource('inbox-tests', \App\Http\Controllers\InboxTestController::class);
    Route::post('inbox-tests/{inboxTest}/send', [\App\Http\Controllers\InboxTestController::class, 'send'])->name('inbox-tests.send');
    Route::post('inbox-tests/{inboxTest}/results', [\App\Http\Controllers\InboxTestController::class, 'updateResults'])->name('inbox-tests.results');

    Route::resource('seed-lists', \App\Http\Controllers\SeedListController::class);

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::get('audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');
    // POST resolve should be restful or at least a post
    Route::post('alerts/{alert}/resolve', [\App\Http\Controllers\AlertController::class, 'resolve'])->name('alerts.resolve');

});

// Webhooks & Tracking
Route::post('/webhooks/ses', \App\Http\Controllers\Webhooks\SESWebhookController::class)->name('webhooks.ses');
Route::get('/t/o/{campaign}/{contact}', [\App\Http\Controllers\TrackingController::class, 'open'])->name('track.open');
Route::get('/t/c', [\App\Http\Controllers\TrackingController::class, 'click'])->name('track.click');
Route::get('/unsubscribe', \App\Http\Controllers\UnsubscribeController::class)->name('unsubscribe');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/contacts/{contact}', [\App\Http\Controllers\ContactController::class, 'show'])->name('contacts.show');
    Route::get('suppressions/export', [\App\Http\Controllers\SuppressionController::class, 'export'])->name('suppressions.export');
    Route::post('suppressions/import', [\App\Http\Controllers\SuppressionController::class, 'import'])->name('suppressions.import');
    Route::resource('domains', \App\Http\Controllers\DomainController::class);
    Route::post('domains/{domain}/verify', [\App\Http\Controllers\DomainController::class, 'verify'])->name('domains.verify');

    Route::get('settings', [\App\Http\Controllers\CompanySettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [\App\Http\Controllers\CompanySettingsController::class, 'update'])->name('settings.update');

    Route::resource('suppressions', \App\Http\Controllers\SuppressionController::class);
});

require __DIR__.'/auth.php';
