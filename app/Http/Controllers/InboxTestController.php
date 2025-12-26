<?php

namespace App\Http\Controllers;

use App\Models\InboxTest;
use App\Models\SeedList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

class InboxTestController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $inboxTests = $company->inboxTests()->latest()->get();
        
        return view('inbox-tests.index', compact('inboxTests'));
    }

    public function create()
    {
        $company = auth()->user()->companies()->first();
        $templates = $company->templates;
        $seedLists = $company->seedLists()->withCount('emails')->get();
        $senders = $company->senders()->with('provider')->get(); // Allow all senders (verified or not)
        
        return view('inbox-tests.create', compact('templates', 'seedLists', 'senders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'seed_list_id' => 'required|exists:seed_lists,id',
            'sender_id' => 'required|exists:senders,id',
            'template_id' => 'nullable|exists:templates,id',
            'subject' => 'required|string|max:255',
        ]);

        $company = auth()->user()->companies()->first();
        $seedList = SeedList::findOrFail($request->seed_list_id);
        
        // Populate seed_emails from the selected list
        $seedEmails = $seedList->emails->pluck('email')->toArray();

        $test = $company->inboxTests()->create([
            'name' => $request->name,
            'seed_emails' => $seedEmails,
            'sender_id' => $request->sender_id,
            'template_id' => $request->template_id,
            'subject' => $request->subject,
            'status' => 'draft',
        ]);

        return redirect()->route('inbox-tests.show', $test)->with('success', 'Inbox test created.');
    }

    public function show(InboxTest $inboxTest)
    {
        $this->authorize('view', $inboxTest);
        $inboxTest->load('template');
        
        $stats = $inboxTest->getPlacementStats();
        $providerStats = $inboxTest->getProviderStats();
        
        $sender = $inboxTest->sender ?? $inboxTest->company->senders()->first();
        $authCheck = $sender ? $sender->checkAuth() : ['spf' => false, 'dkim' => false, 'dmarc' => false, 'ssl' => true];

        return view('inbox-tests.show', compact('inboxTest', 'stats', 'providerStats', 'authCheck'));
    }

    public function send(InboxTest $inboxTest)
    {
        $this->authorize('update', $inboxTest);

        if ($inboxTest->status !== 'draft') {
            return back()->with('error', 'Only draft tests can be sent.');
        }

        $template = $inboxTest->template;
        $sender = $inboxTest->sender ?? $inboxTest->company->senders()->first();

        if (!$sender) {
             return back()->with('error', 'No verified sender selected or found.');
        }
        
        // Use provider associated with sender
        $provider = $sender->provider;
        $failedCount = 0;
        
        try {
            $mailerName = $provider ? \App\Services\MailService::configureMailer($provider) : null;
            // Use the configured mailer or fallback to default
            $mailInstance = $mailerName ? Mail::mailer($mailerName) : Mail::to([]); // Fallback to empty if no provider? Or default?
            
            if (!$mailerName) {
                 // Fallback to system default if no provider specific config
                 $mailInstance = Mail::mailer(config('mail.default'));
            }
    
            foreach ($inboxTest->seed_emails as $email) {
                try {
                    $mailInstance->to($email)->send(new \App\Mail\InboxTestMail($inboxTest, $template, $sender)); // Using Mailable is better but inline is ok for now if we keep inline
                    
                    // We need to support the inline closure style if we don't have a Mailable class
                    // Re-implementing inline send for now to match previous style but with correct instance
                    /* 
                    $mailInstance->send([], [], function ($message) use ($email, $inboxTest, $template, $sender) {
                        $message->to($email)
                            ->from($sender->from_email ?? 'test@example.com', $sender->from_name ?? 'Test')
                            ->subject($inboxTest->subject)
                            ->html($template->content); 
                    });
                    */
                    // Actually, let's keep the closure for minimal refactor impact, but using $mailInstance correctly
                     $mailInstance->send([], [], function ($message) use ($email, $inboxTest, $template, $sender) {
                        $message->to($email)
                            ->from($sender->from_email ?? 'test@example.com', $sender->from_name ?? 'Test')
                            ->subject($inboxTest->subject)
                            ->html($template->content ?? 'Test Email'); 
                    });

                    \Log::info("Inbox Test sent to $email via " . ($mailerName ?? 'default'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send inbox test to $email: " . $e->getMessage());
                    // Record failure but don't stop loop
                    $failedCount++;
                }
            }
        } catch (\Throwable $e) {
             \Log::error("Critical Error in InboxTest sending: " . $e->getMessage());
             return back()->with('error', 'Critical Error: ' . $e->getMessage());
        }

        $inboxTest->update([
            'status' => $failedCount === count($inboxTest->seed_emails) ? 'failed' : 'sent',
            'sent_at' => now(),
            // Store simple stats about immediate failures
            'results' => ['sent_count' => count($inboxTest->seed_emails) - $failedCount, 'failed_count' => $failedCount]
        ]);

        return redirect()->route('inbox-tests.show', $inboxTest)
            ->with('success', 'Test emails sent! Results will be tracked via webhooks or manual entry.');
    }

    public function updateResults(Request $request, InboxTest $inboxTest)
    {
        $this->authorize('update', $inboxTest);

        $request->validate([
            'results' => 'required|array',
        ]);

        $inboxTest->update([
            'results' => $request->results,
            'status' => 'completed',
        ]);

        return back()->with('success', 'Results updated.');
    }
}
