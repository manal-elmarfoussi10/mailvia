<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $companyId = session('company_id');

            // Default to first company if not set
            if (! $companyId) {
                $company = $user->companies()->first();
                if ($company) {
                    session(['company_id' => $company->id]);
                    $companyId = $company->id;
                }
            }

            // Validate and Share
            if ($companyId) {
                // We use find to ensure it exists.
                // In production with many users, cache this or rely on relation check.
                $currentCompany = \App\Models\Company::find($companyId);

                // Ensure user belongs to this company
                if ($currentCompany && $user->companies()->where('id', $companyId)->exists()) {
                    \Illuminate\Support\Facades\View::share('currentCompany', $currentCompany);
                } else {
                    // Invalid company for this user
                    session()->forget('company_id');
                    // If no valid company, redirect to dashboard or company selection
                    if (!$user->companies()->exists()) {
                        return redirect()->route('dashboard')->with('error', 'No companies found. Please create a company first.');
                    } else {
                        // Redirect to dashboard to select company
                        return redirect()->route('dashboard')->with('error', 'Invalid company selected.');
                    }
                }
            } else {
                // No company set, check if user has any companies
                if (!$user->companies()->exists()) {
                    return redirect()->route('companies.create')->with('error', 'Please create a company to continue.');
                } else {
                    return redirect()->route('dashboard')->with('info', 'Please select a company.');
                }
            }
        }

        return $next($request);
    }
}
