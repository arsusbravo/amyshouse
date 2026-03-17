<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Material;
use App\Models\ProductType;
use Inertia\Inertia;
use Inertia\Response;

class AttributeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Attributes', [
            'types' => ProductType::orderBy('sort_order')->get(),
            'materials' => Material::orderBy('sort_order')->get(),
            'colors' => Color::orderBy('sort_order')->get(),
        ]);
    }
}