<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List categories with search. Staff may view this page read-only;
     * every mutating action is admin-only via route middleware.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['sometimes', 'string', 'max:255'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));

        $categories = Category::query()
            ->withCount('machines')
            // SECURITY: bound LIKE parameter; "' OR 1=1 --" is plain text.
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form to create a new category (admin only).
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Persist a new category (admin only).
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        Category::create($request->validated());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show one category with the machines that belong to it.
     */
    public function show(Category $category): View
    {
        $category->load(['machines' => fn ($query) => $query->latest()]);

        return view('categories.show', ['category' => $category]);
    }

    /**
     * Show the edit form (admin only).
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', ['category' => $category]);
    }

    /**
     * Update a category (admin only).
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Delete a category (admin only). The foreign key on machines is
     * restrictOnDelete, so a category that still has machines cannot be
     * removed — that database error is turned into a friendly message.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $exception) {
            // SECURITY/UX: the foreign key (restrict) blocked the delete
            // because machines still use this category. Show a message
            // instead of a 500 error.
            return back()->with('error', 'Kategori masih memiliki mesin dan tidak bisa dihapus.');
        }
    }
}
