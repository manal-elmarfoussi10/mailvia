<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TemplateController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $templates = $company->templates()->latest()->paginate(20);
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content_html' => 'nullable|string',
            'content_text' => 'nullable|string',
        ]);

        $company = auth()->user()->companies()->first();
        $company->templates()->create($request->only('name', 'subject', 'content_html', 'content_text'));

        return redirect()->route('templates.index')->with('success', 'Template created successfully.');
    }

    public function show(Template $template)
    {
        $this->authorize('view', $template);
        return view('templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        $this->authorize('view', $template);
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $this->authorize('update', $template);

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content_html' => 'nullable|string',
            'content_text' => 'nullable|string',
        ]);

        $template->update($request->only('name', 'subject', 'content_html', 'content_text'));

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(Template $template)
    {
        $this->authorize('delete', $template);
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
