<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies;
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        $company = \App\Models\Company::create($data);
        auth()->user()->companies()->attach($company);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function edit(\App\Models\Company $company)
    {
        $this->authorize('view', $company);
        return view('companies.edit', compact('company'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Company $company)
    {
        $this->authorize('update', $company);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(\App\Models\Company $company)
    {
        $this->authorize('delete', $company);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }

    public function switch(\App\Models\Company $company)
    {
        if (! auth()->user()->companies->contains($company)) {
            abort(403);
        }

        session(['company_id' => $company->id]);

        return back()->with('success', 'Switched to ' . $company->name);
    }
}
