<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\MailService;
use App\Models\Provider;

class AdminMailTestController extends Controller
{
    public function index()
    {
        // Simple UI to test email
        return "<form method='POST' action='".route('admin.mail.test.send')."'>
            ".csrf_field()."
            <label>To Email: <input type='email' name='to' required></label><br>
            <label>Provider ID (Optional): <input type='number' name='provider_id'></label><br>
            <button type='submit'>Send Test</button>
        </form>";
    }

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'provider_id' => 'nullable|exists:providers,id',
        ]);

        try {
            $mailerName = null;
            if ($request->provider_id) {
                $provider = Provider::findOrFail($request->provider_id);
                $mailerName = MailService::configureMailer($provider);
            }

            $mail = $mailerName ? Mail::mailer($mailerName) : Mail::to($request->to); 
            
            // If we used a specific mailer, we still need to set the recipient
            if ($mailerName) {
                $mail = $mail->to($request->to);
            }

            $mail->send([], [], function ($message) use ($request) {
                $message->to($request->to)
                    ->subject('Admin Mail Test ' . now())
                    ->html('This is a test email from the admin panel.');
            });

            return "Email sent successfully using driver: " . ($mailerName ?? config('mail.default'));

        } catch (\Exception $e) {
            return "Failed: " . $e->getMessage() . "<br><pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
