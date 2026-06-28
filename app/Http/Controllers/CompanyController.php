<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('projects')->latest()->paginate(20);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Company::create($request->only(['name', 'industry', 'contact_email', 'contact_phone', 'address', 'status']));
        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function show(Company $company)
    {
        $company->load(['projects.workLogs', 'projects.tasks']);
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $company->update($request->only(['name', 'industry', 'contact_email', 'contact_phone', 'address', 'status']));
        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
