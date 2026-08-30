<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use Livewire\Component;
use Livewire\WithPagination;

class ShopFilter extends Component
{
    use WithPagination;

    public string $category  = '';
    public int    $maxPrice  = 3000;
    public string $author    = '';
    public string $publisher = '';
    public string $sort      = 'featured';
    public string $search    = '';

    protected $queryString = [
        'category'  => ['except' => ''],
        'maxPrice'  => ['except' => 3000],
        'author'    => ['except' => ''],
        'publisher' => ['except' => ''],
        'sort'      => ['except' => 'featured'],
        'search'    => ['except' => ''],
    ];

    public function updatedCategory()
    {
        $this->resetPage();
    }
    public function updatedMaxPrice()
    {
        $this->resetPage();
    }
    public function updatedAuthor()
    {
        $this->resetPage();
    }
    public function updatedPublisher()
    {
        $this->resetPage();
    }
    public function updatedSort()
    {
        $this->resetPage();
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['category', 'maxPrice', 'author', 'publisher', 'sort', 'search']);
        $this->maxPrice = 3000;
        $this->resetPage();
    }

    public function render()
    {
        $filters = [
            'category'  => $this->category,
            'max_price' => $this->maxPrice,
            'author'    => $this->author,
            'publisher' => $this->publisher,
            'sort'      => $this->sort,
            'search'    => $this->search,
        ];

        return view('livewire.shop-filter', [
            'products'   => Product::filter($filters)->with(['category', 'author', 'publisher'])->paginate(12),
            'categories' => Category::active()->get(),
            'authors'    => Author::active()->get(),
            'publishers' => Publisher::active()->get(),
        ]);
    }
}
