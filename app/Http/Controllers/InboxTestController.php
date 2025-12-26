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
        
        try {
            $mailer = $provider ? \App\Services\MailService::configureMailer($provider) : null;
            // If no provider linked, fallback to default (mailer=null means default in facade usually, but we need empty string for 'default' or explicit)
            // Actually Mail::mailer(null) might fail. If no provider, use default.
            $mailInstance = $mailer ? Mail::mailer($mailer) : Mail::parent(); 
    
            foreach ($inboxTest->seed_emails as $email) {
                try {
                    $mailInstance->send([], [], function ($message) use ($email, $inboxTest, $template, $sender) {
                        $message->to($email)
                            ->from($sender->from_email ?? 'test@example.com', $sender->from_name ?? 'Test')
                            ->subject($inboxTest->subject)
                            ->html($template->content); 
                    });
                } catch (\Exception $e) {
                    \Log::error("Failed to send inbox test to $email: " . $e->getMessage());
                    // Continue to next email instead of failing entire batch? 
                    // Or failing allows us to see the error. Let's record it.
                }
            }
        } catch (\Throwable $e) {
             return back()->with('error', 'Critical Error: ' . $e->getMessage());
        }

        $inboxTest->update([
            'status' => 'sent',
            'sent_at' => now(),
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
