<?php

namespace App\Livewire\User;

use App\Models\Blog as BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $category = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => null],
    ];

    public function mount(): void
    {
        if ($this->category !== null) {
            $this->category = (int) $this->category;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatedCategory($value): void
    {
        $this->category = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function setCategory(?int $categoryId): void
    {
        $this->category = $categoryId;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->category = null;
        $this->resetPage();
    }

    public function render()
    {
        $blogCategories = BlogCategory::orderBy('name')->get(['id', 'name']);

        $blogs = BlogPost::query()
            ->with('blogCategory:id,name')
            ->where('status', 'PUBLISHED')
            ->when($this->category, fn ($q) => $q->where('blog_category_id', $this->category))
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9);

        $totalPublished = BlogPost::where('status', 'PUBLISHED')->count();

        return view('livewire.user.blog', [
            'blogs' => $blogs,
            'blogCategories' => $blogCategories,
            'totalPublished' => $totalPublished,
        ]);
    }
}
