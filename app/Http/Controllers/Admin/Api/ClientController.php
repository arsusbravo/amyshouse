<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'group_id' => 'nullable|exists:groups,id',
            'password' => 'nullable|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'group_id' => $validated['group_id'] ?? null,
            'password' => Hash::make($validated['password'] ?? Str::random(12)),
            'is_active' => true,
            'is_admin' => false,
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created.');
    }

    public function update(Request $request, User $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($client->id)],
            'phone' => 'nullable|string|max:50',
            'group_id' => 'nullable|exists:groups,id',
            'password' => 'nullable|string|min:6',
            'is_active' => 'boolean',
        ]);

        $data = collect($validated)->except('password')->toArray();

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $client->update($data);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated.');
    }

    public function toggleActive(User $client)
    {
        $client->update(['is_active' => ! $client->is_active]);

        return back()->with('success', $client->is_active ? 'Client activated.' : 'Client deactivated.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'Empty CSV file.']);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $requiredColumns = ['name', 'email'];
        $missingColumns = array_diff($requiredColumns, $header);
        if (! empty($missingColumns)) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing required columns: ' . implode(', ', $missingColumns),
            ]);
        }

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            if (empty($data['email']) || empty($data['name'])) {
                $skipped++;
                continue;
            }

            if (User::where('email', $data['email'])->exists()) {
                $skipped++;
                continue;
            }

            $groupId = null;
            if (! empty($data['group'])) {
                $group = Group::where('name', 'like', '%' . trim($data['group']) . '%')->first();
                $groupId = $group?->id;
            }

            User::create([
                'name' => trim($data['name']),
                'email' => trim($data['email']),
                'phone' => isset($data['phone']) ? trim($data['phone']) : null,
                'group_id' => $groupId,
                'password' => Hash::make($data['password'] ?? Str::random(12)),
                'is_active' => true,
                'is_admin' => false,
            ]);

            $imported++;
        }

        fclose($handle);

        return back()->with('success', "Imported {$imported} clients, skipped {$skipped}.");
    }
}