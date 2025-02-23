<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::withCount('tasks')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'type' => $category->type,
                    'color' => $category->color,
                    'description' => $category->description,
                    'is_active' => $category->is_active,
                    'tasks_count' => $category->tasks_count,
                    'created_at' => $category->created_at->format('d M Y')
                ])
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'type' => 'required|string|in:content,task',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => true
        ]);

        return redirect()->route('categories.index')
            ->with('message', 'Kategori berhasil dibuat');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'description' => $category->description,
                'color' => $category->color,
                'is_active' => $category->is_active
            ]
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'type' => 'required|string|in:content,task',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('categories.index')
            ->with('message', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        if ($category->tasks()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang memiliki task');
        }
        
        $category->delete();
        return redirect()->route('categories.index')
            ->with('message', 'Kategori berhasil dihapus');
    }
} 