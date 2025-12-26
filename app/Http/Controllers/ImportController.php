<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Jobs\ProcessImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $imports = $company->imports()->latest()->paginate(10);
        return view('imports.index', compact('imports'));
    }

    public function create()
    {
        return view('imports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240', // 10MB max
            'contact_list_id' => 'nullable|exists:contact_lists,id',
        ]);

        $company = auth()->user()->companies()->first();
        
        $file = $request->file('file');
        // Store in private folder
        $path = $file->store('imports/' . $company->id); 
        
        $import = $company->imports()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'uploaded',
            'contact_list_id' => $request->contact_list_id,
        ]);

        return redirect()->route('imports.edit', $import)->with('success', 'File uploaded. Please map columns.');
    }

    public function edit(Import $import)
    {
        $this->authorize('view', $import);

        if ($import->status !== 'uploaded') {
            return redirect()->route('imports.index')->with('info', 'Import is already processing or completed.');
        }

        // Read headers from file to show mapping UI
        // We use a simple read here, assuming first row is header
        $headings = [];
        try {
            // Auto-detect delimiter for CSVs
            $extension = pathinfo($import->file_path, PATHINFO_EXTENSION);
            $readerType = null;
            
            if (strtolower($extension) === 'csv') {
                $readerType = \Maatwebsite\Excel\Excel::CSV;
                // Simple sniff
                $content = file_get_contents(Storage::path($import->file_path));
                $firstLine = strtok($content, "\n");
                if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                     Config::set('excel.imports.csv.delimiter', ';');
                } else {
                     Config::set('excel.imports.csv.delimiter', ','); // Default
                }
            }

            $data = Excel::toArray([], $import->file_path, null, $readerType); 
             if (empty($data)) {
                 $data = Excel::toArray([], $import->file_path);
             }
             
             if (count($data) > 0 && count($data[0]) > 0) {
                 $headings = $data[0][0]; // Sheet 1, Row 1
             }

        } catch (\Exception $e) {
            return back()->with('error', 'Could not read file headers: ' . $e->getMessage());
        }

        // predefined contact fields
        $fields = [
            'email' => 'Email Address (Required)',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'tags' => 'Tags (comma separated)',
        ];

        return view('imports.map', compact('import', 'headings', 'fields'));
    }

    public function map(Request $request, Import $import)
    {
        $this->authorize('update', $import);

        $request->validate([
            'mapping' => 'required|array',
            'mapping.email' => 'required', // Must map email
        ]);

        $import->update([
            'mapping' => $request->mapping,
            'status' => 'processing', // Queued
        ]);

        // Dispatch Job
        ProcessImportJob::dispatch($import);

        return redirect()->route('imports.index')->with('success', 'Import started in background.');
    }
}
