<?php

namespace App\Http\Controllers;

use App\Models\Suppression;
use Illuminate\Http\Request;

class SuppressionController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->companies()->first();
        
        $suppressions = $company->suppressions()
            ->when($request->search, function($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->when($request->reason, function($query, $reason) {
                return $query->where('reason', $reason);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $stats = $company->suppressions()
            ->selectRaw('reason, count(*) as count')
            ->groupBy('reason')
            ->pluck('count', 'reason')
            ->toArray();

        return view('suppressions.index', compact('suppressions', 'stats'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();

        $data = $request->validate([
            'email' => 'required|email',
            'reason' => 'nullable|string|max:255',
        ]);

        $company->suppressions()->updateOrCreate(
            ['email' => strtolower($data['email'])],
            ['reason' => $data['reason'] ?? 'manual', 'suppressed_at' => now()]
        );

        return redirect()->route('suppressions.index')->with('success', 'Email added to suppression list.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $company = auth()->user()->companies()->first();
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Assuming first row is header

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $email = strtolower(trim($row[0] ?? ''));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $company->suppressions()->updateOrCreate(
                    ['email' => $email],
                    [
                        'reason' => trim($row[1] ?? 'manual-import'),
                        'suppressed_at' => now()
                    ]
                );
                $count++;
            }
        }
        fclose($handle);

        return redirect()->route('suppressions.index')->with('success', "Imported {$count} emails to suppression list.");
    }

    public function export()
    {
        $company = auth()->user()->companies()->first();
        $suppressions = $company->suppressions()->latest()->get();

        $filename = "suppression-list-" . now()->format('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($suppressions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Reason', 'Suppressed At']);

            foreach ($suppressions as $suppression) {
                fputcsv($file, [
                    $suppression->email,
                    $suppression->reason,
                    $suppression->suppressed_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Suppression $suppression)
    {
        $this->authorize('delete', $suppression);
        $suppression->delete();
        
        return redirect()->route('suppressions.index')->with('success', 'Email removed from suppression list.');
    }
}
