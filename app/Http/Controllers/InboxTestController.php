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
        // Validate custom sender fields
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'seed_list_id' => 'required|exists:seed_lists,id',
            'template_id' => 'nullable|exists:templates,id',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
        ]);

        $company = auth()->user()->companies()->first();
        
        // Fetch seed emails
        $seedList = SeedList::with('emails')->findOrFail($data['seed_list_id']);
        $seedEmails = $seedList->emails->pluck('email')->toArray();
        
        // Debug Log
        \Log::info("InboxTest Creation Debug", [
            'seed_list_id' => $data['seed_list_id'],
            'seed_emails_count' => count($seedEmails),
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email']
        ]);

        if (empty($seedEmails)) {
            return back()->with('error', 'The selected seed list has no emails.');
        }

        try {
            $test = $company->inboxTests()->create([
                'name' => $data['name'],
                // 'seed_list_id' => $data['seed_list_id'], // Removed as column doesn't exist
                'template_id' => $data['template_id'],
                'subject' => $data['subject'],
                'from_name' => $data['from_name'],
                'from_email' => $data['from_email'],
                'status' => 'draft',
                'seed_emails' => $seedEmails,
                'sender_id' => null, // Explicitly set to null if column exists
            ]);
        } catch (\Exception $e) {
            \Log::error("InboxTest Creation Failed: " . $e->getMessage());
            return back()->with('error', 'Creation failed: ' . $e->getMessage());
        }

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
        $template = $inboxTest->template;

        // Warning: using strict ENV mailer
        
        $failedCount = 0;
        $firstError = null;

        try {
            // Log config for debug
            $conf = config('mail.mailers.smtp');
            \Log::info("InboxTest: Pre-flight via ENV", [
                'host' => $conf['host'] ?? 'N/A',
                'port' => $conf['port'] ?? 'N/A', 
                'user' => $conf['username'] ?? 'N/A'
            ]);

            foreach ($inboxTest->seed_emails as $email) {
                try {
                    // Use global Mail::send() which uses default mailer from .env
                    Mail::send([], [], function ($message) use ($email, $inboxTest, $template) {
                        $message->to($email)
                            ->from(
                                $inboxTest->from_email ?? config('mail.from.address'),
                                $inboxTest->from_name ?? config('mail.from.name')
                            )
                            ->subject($inboxTest->subject)
                            ->html($template?->content_html ?? $template?->content_text ?? '<p>Test Email</p>');
                    });

                    \Log::info("InboxTest: Sent to $email");
                } catch (\Exception $e) {
                    $failedCount++;
                    if (!$firstError) $firstError = $e->getMessage();
                    \Log::error("InboxTest: Failed to $email: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            \Log::error("InboxTest: Critical Error: " . $e->getMessage());
            return back()->with('error', 'Critical Error: ' . $e->getMessage());
        }

        // Initialize results for all seed emails as 'missing' (pending check)
        $initialResults = [];
        foreach ($inboxTest->seed_emails as $email) {
            $initialResults[$email] = 'missing'; 
        }

        if ($failedCount === count($inboxTest->seed_emails)) {
            $inboxTest->update(['status' => 'failed', 'sent_at' => now(), 'results' => $initialResults]);
            return back()->with('error', 'All emails failed. First error: ' . $firstError);
        }

        $inboxTest->update([
            'status' => 'sent',
            'sent_at' => now(),
            'results' => $initialResults // Store map of email => status
        ]);

        return redirect()->route('inbox-tests.show', $inboxTest)
            ->with('success', 'Test emails sent! Please check your seed mailboxes and update statuses below.');
    }

    public function updateResults(Request $request, InboxTest $inboxTest)
    {
        $this->authorize('update', $inboxTest);

        $request->validate([
            'results' => 'required|array',
        ]);

        // Merge existing results with new ones (or just overwrite)
        // Ensure we strictly follow email => status format
        $inboxTest->update([
            'results' => $request->results,
            'status' => 'completed',
        ]);

        return back()->with('success', 'Results updated successfully.');
    }
}
