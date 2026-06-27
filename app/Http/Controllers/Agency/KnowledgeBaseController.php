<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $query = KnowledgeBaseArticle::where('tenant_id', Auth::user()->tenant_id)
            ->with('author')
            ->orderByDesc('updated_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($sq) => $sq->where('title', 'like', "%{$q}%")->orWhere('excerpt', 'like', "%{$q}%"));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $articles   = $query->get();
        $categories = KnowledgeBaseArticle::where('tenant_id', Auth::user()->tenant_id)
            ->whereNotNull('category')->distinct()->pluck('category');

        return view('agency.knowledge-base.index', compact('articles', 'categories'));
    }

    public function create(): \Illuminate\View\View
    {
        $categories = KnowledgeBaseArticle::where('tenant_id', Auth::user()->tenant_id)
            ->whereNotNull('category')->distinct()->pluck('category');
        return view('agency.knowledge-base.create', compact('categories'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'excerpt'   => 'nullable|string',
            'content'   => 'required|string',
            'category'  => 'nullable|string|max:100',
            'is_public' => 'nullable|boolean',
        ]);

        $article = KnowledgeBaseArticle::create(array_merge($data, [
            'tenant_id'    => Auth::user()->tenant_id,
            'author_id'    => Auth::id(),
            'slug'         => Str::slug($data['title']),
            'is_public'    => $request->boolean('is_public'),
            'published_at' => $request->boolean('is_public') ? now() : null,
        ]));

        return redirect()->route('agency.knowledge-base.show', $article)->with('success', 'Article published.');
    }

    public function show(KnowledgeBaseArticle $knowledgeBase): \Illuminate\View\View
    {
        return view('agency.knowledge-base.show', ['article' => $knowledgeBase]);
    }

    public function edit(KnowledgeBaseArticle $knowledgeBase): \Illuminate\View\View
    {
        $categories = KnowledgeBaseArticle::where('tenant_id', Auth::user()->tenant_id)
            ->whereNotNull('category')->distinct()->pluck('category');
        return view('agency.knowledge-base.edit', ['article' => $knowledgeBase, 'categories' => $categories]);
    }

    public function update(Request $request, KnowledgeBaseArticle $knowledgeBase): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'excerpt'   => 'nullable|string',
            'content'   => 'required|string',
            'category'  => 'nullable|string|max:100',
            'is_public' => 'nullable|boolean',
        ]);

        $wasPublic = $knowledgeBase->is_public;
        $knowledgeBase->update(array_merge($data, [
            'is_public'    => $request->boolean('is_public'),
            'published_at' => (!$wasPublic && $request->boolean('is_public')) ? now() : $knowledgeBase->published_at,
        ]));

        return redirect()->route('agency.knowledge-base.show', $knowledgeBase)->with('success', 'Article updated.');
    }

    public function destroy(KnowledgeBaseArticle $knowledgeBase): \Illuminate\Http\RedirectResponse
    {
        $knowledgeBase->delete();
        return redirect()->route('agency.knowledge-base.index')->with('success', 'Article deleted.');
    }
}
