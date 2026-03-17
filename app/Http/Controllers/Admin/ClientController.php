<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::where('is_admin', false)
            ->with('group')
            ->withCount('orders')
            ->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($groupId = $request->input('group_id')) {
            $query->where('group_id', $groupId);
        }

        $clients = $query->paginate(20)->through(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'group_name' => $u->group?->name,
            'group_id' => $u->group_id,
            'is_active' => $u->is_active,
            'orders_count' => $u->orders_count,
        ]);

        $groups = Group::orderBy('sort_order')->get()->map(fn (Group $g) => [
            'id' => $g->id,
            'name' => $g->name,
        ]);

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'groups' => $groups,
            'filters' => $request->only(['search', 'group_id']),
        ]);
    }

    public function show(User $client): Response
    {
        $client->load('group');

        $orders = $client->orders()
            ->with('group')
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->through(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'status' => $o->status->value,
                'status_label' => $o->status->labelEn(),
                'total_amount' => $o->total_amount,
                'items_count' => $o->items_count,
                'group_name' => $o->group?->name,
                'created_at' => $o->created_at->toISOString(),
            ]);

        return Inertia::render('Admin/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'group_name' => $client->group?->name,
                'group_id' => $client->group_id,
                'is_active' => $client->is_active,
            ],
            'orders' => $orders,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Clients/Form', [
            'client' => null,
            'groups' => Group::orderBy('sort_order')->get()->map(fn (Group $g) => [
                'id' => $g->id,
                'name' => $g->name,
            ]),
        ]);
    }

    public function edit(User $client): Response
    {
        return Inertia::render('Admin/Clients/Form', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'group_id' => $client->group_id,
                'is_active' => $client->is_active,
            ],
            'groups' => Group::orderBy('sort_order')->get()->map(fn (Group $g) => [
                'id' => $g->id,
                'name' => $g->name,
            ]),
        ]);
    }
}