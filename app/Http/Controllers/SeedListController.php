<?php

namespace App\Http\Controllers;

use App\Models\SeedList;
use App\Models\SeedListEmail;
use Illuminate\Http\Request;

class SeedListController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $seedLists = $company->seedLists()->withCount('emails')->get();
        return view('seed-lists.index', compact('seedLists'));
    }

    public function create()
    {
        return view('seed-lists.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'emails' => 'required|string',
        ]);

        $seedList = $company->seedLists()->create([
            'name' => $request->name,
        ]);

        $this->processEmails($seedList, $request->emails);

        return redirect()->route('seed-lists.index')->with('success', 'Seed list created.');
    }

    public function edit(SeedList $seedList)
    {
        $this->authorize('view', $seedList);
        $emails = $seedList->emails->pluck('email')->implode("\n");
        return view('seed-lists.edit', compact('seedList', 'emails'));
    }

    public function update(Request $request, SeedList $seedList)
    {
        $this->authorize('update', $seedList);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'emails' => 'required|string',
        ]);

        $seedList->update(['name' => $request->name]);
        
        // Refresh emails
        $seedList->emails()->delete();
        $this->processEmails($seedList, $request->emails);

        return redirect()->route('seed-lists.index')->with('success', 'Seed list updated.');
    }

    public function destroy(SeedList $seedList)
    {
        $this->authorize('delete', $seedList);
        $seedList->delete();
        return redirect()->route('seed-lists.index')->with('success', 'Seed list deleted.');
    }

    protected function processEmails(SeedList $seedList, string $emailString)
    {
        $emails = preg_split('/[\s,]+/', $emailString, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $type = 'custom';
                if (str_contains($email, 'gmail.com')) $type = 'gmail';
                elseif (str_contains($email, 'outlook.com') || str_contains($email, 'hotmail.com')) $type = 'outlook';
                elseif (str_contains($email, 'yahoo.com')) $type = 'yahoo';

                $seedList->emails()->create([
                    'email' => $email,
                    'mailbox_type' => $type,
                ]);
            }
        }
    }
}
