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
                    Mail::send([], [], function ($message) use ($email, $inboxTest, $template, $sender) {
                        $message->to($email)
                            ->from(
                                $sender->email ?? config('mail.from.address'),
                                $sender->name ?? config('mail.from.name')
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

        if ($failedCount === count($inboxTest->seed_emails)) {
            $inboxTest->update(['status' => 'failed', 'sent_at' => now()]);
            return back()->with('error', 'All emails failed. First error: ' . $firstError);
        }

        $inboxTest->update([
            'status' => 'sent',
            'sent_at' => now(),
            'results' => ['sent_count' => count($inboxTest->seed_emails) - $failedCount, 'failed_count' => $failedCount]
        ]);

        return redirect()->route('inbox-tests.show', $inboxTest)
            ->with('success', 'Test emails sent via default pipeline! (Success: ' . (count($inboxTest->seed_emails) - $failedCount) . ', Failed: ' . $failedCount . ')');
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
