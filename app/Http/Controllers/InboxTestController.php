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
        
        return view('inbox-tests.create', compact('templates', 'seedLists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'seed_list_id' => 'required|exists:seed_lists,id',
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
        
        return view('inbox-tests.show', compact('inboxTest', 'stats', 'providerStats'));
    }

    public function send(InboxTest $inboxTest)
    {
        $this->authorize('update', $inboxTest);

        if ($inboxTest->status !== 'draft') {
            return back()->with('error', 'Only draft tests can be sent.');
        }

        $template = $inboxTest->template;
        $sender = $inboxTest->company->senders()->first();

        foreach ($inboxTest->seed_emails as $email) {
            try {
                Mail::send([], [], function ($message) use ($email, $inboxTest, $template, $sender) {
                    $message->to($email)
                        ->from($sender->from_email ?? 'test@example.com', $sender->from_name ?? 'Test')
                        ->subject($inboxTest->subject)
                        ->html($template->content_html ?? '<p>Test email</p>');
                });
            } catch (\Exception $e) {
                \Log::error("Failed to send inbox test to {$email}: " . $e->getMessage());
            }
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
