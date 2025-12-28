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
        // Simple UI to test email via ENV pipeline
        return "<div style='font-family:sans-serif; padding:20px;'>
        <h2>Admin Mail Test (ENV Pipeline)</h2>
        <form method='POST' action='".route('admin.mail.test.send')."'>
            ".csrf_field()."
            <label>To Email: <input type='email' name='to' required placeholder='name@example.com' style='padding:5px; width:300px;'></label>
            <br><br>
            <button type='submit' style='padding:10px 20px; cursor:pointer;'>Send Test Email</button>
        </form>
        </div>";
    }

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
        ]);

        try {
            // ENV-ONLY Mode: Use the default mailer configured in .env
            // Logging details
            $config = config('mail.mailers.smtp');
            \Log::info("AdminTest: Attempting send to {$request->to}", [
                'host' => $config['host'] ?? 'N/A',
                'user' => $config['username'] ?? 'N/A'
            ]);

            Mail::send([], [], function ($message) use ($request) {
                $message->to($request->to)
                    ->from(config('mail.from.address'), config('mail.from.name') . ' (Admin Test)')
                    ->subject('Admin Mail Test: ' . now())
                    ->html('<h3>This is a test email from the admin panel.</h3><p>If you see this, the ENV pipeline is working.</p>');
            });

            return back()->with('success', "Email sent successfully to {$request->to} using default mailer.");

        } catch (\Exception $e) {
            \Log::error("AdminTest: Failed to {$request->to}: " . $e->getMessage());
            return "<h3>Failed</h3><p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
