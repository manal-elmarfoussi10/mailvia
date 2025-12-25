<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SegmentController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $segments = $company->segments()->paginate(20);
        return view('segments.index', compact('segments'));
    }

    public function create()
    {
        return view('segments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'criteria' => 'required|array',
            // Example criteria: ['tag' => 'vip', 'status' => 'subscribed']
        ]);

        $company = auth()->user()->companies()->first();
        $company->segments()->create($data = $request->only('name', 'criteria'));

        return redirect()->route('segments.index')->with('success', 'Segment created.');
    }

    public function edit(Segment $segment)
    {
        $this->authorize('view', $segment);
        return view('segments.edit', compact('segment'));
    }

    public function update(Request $request, Segment $segment)
    {
        $this->authorize('update', $segment);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'criteria' => 'required|array',
        ]);

        $segment->update($request->only('name', 'criteria'));

        return redirect()->route('segments.index')->with('success', 'Segment updated.');
    }

    public function destroy(Segment $segment)
    {
        $this->authorize('delete', $segment);
        $segment->delete();
        return redirect()->route('segments.index')->with('success', 'Segment deleted.');
    }

    public function count(Request $request)
    {
        $company = auth()->user()->companies()->first();
        $criteria = $request->input('criteria', []);
        
        $segment = new Segment(['criteria' => $criteria, 'company_id' => $company->id]);
        $count = $segment->contact_count;

        return response()->json(['count' => $count]);
    }
}
