<?php

namespace App\Http\Livewire;

use App\Models\Hostel;
use App\Models\RoomCategory;
use Livewire\Component;
use Livewire\WithPagination;

class HomeComponent extends Component
{
    use WithPagination;

    protected $layout = 'layouts.app';

    public $hostel_name = '';
    public $institution = '';
    public $room_type = '';
    public $gender_category = '';
    public $min_price = '';
    public $max_price = '';
    public $sort_by = 'name_az';

    protected $queryString = [
        'institution' => ['except' => ''],
        'room_type' => ['except' => ''],
        'gender_category' => ['except' => ''],
        'hostel_name' => ['except' => ''],
        'min_price' => ['except' => ''],
        'max_price' => ['except' => ''],
        'sort_by' => ['except' => 'name_az'],
        'page' => ['except' => 1],
    ];

    public $institutions = [];

    public function mount()
    {
        $this->institutions = Hostel::institutions();
    }

    public $genders = [
        '' => 'Select Gender',
        'Male' => 'Male',
        'Female' => 'Female',
    ];

    public $sortOptions = [
        'name_az' => 'A to Z',
        'name_za' => 'Z to A',
        'price_asc' => 'Lowest Price',
        'price_desc' => 'Highest Price',
        'rating' => 'Top Rated',
        'newest' => 'Newest First',
    ];

    public function updatedPage()
    {
        $this->emit('resetTinySliders');
    }

    public function resetFilters()
    {
        $this->reset('hostel_name', 'institution', 'room_type', 'gender_category', 'min_price', 'max_price', 'sort_by');
        $this->resetPage();
    }

    public function render()
    {
        $query = Hostel::query()
            ->with(['rooms' => function ($q) {
                $q->withAvailability();
            }, 'images'])
            ->where('is_active', true);

        if ($this->hostel_name !== '') {
            $query->where('name', 'like', '%' . $this->hostel_name . '%');
        }

        if ($this->institution !== '') {
            $query->where('institution', $this->institution);
        }

        if ($this->room_type !== '') {
            $query->whereHas('rooms', function ($q) {
                $q->where('room_type', $this->room_type)
                    ->where('is_active', true)
                    ->hasAvailability();
            });
        }

        if ($this->gender_category !== '') {
            $query->whereHas('rooms', function ($q) {
                $q->where('gender_category', $this->gender_category)
                    ->where('is_active', true)
                    ->hasAvailability();
            });
        }

        $minPrice = is_numeric($this->min_price) ? (float) $this->min_price : null;
        $maxPrice = is_numeric($this->max_price) ? (float) $this->max_price : null;

        if ($minPrice !== null) {
            $query->where('starting_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('starting_price', '<=', $maxPrice);
        }

        switch ($this->sort_by) {
            case 'price_asc':
                $query->orderBy('starting_price');
                break;
            case 'price_desc':
                $query->orderByDesc('starting_price');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'name_za':
                $query->orderByDesc('name');
                break;
            default:
                $query->orderBy('name');
        }

        $hostels = $query->paginate(9);

        $roomTypes = ['' => 'Select Room Type']
            + RoomCategory::active()->ordered()->pluck('name', 'name')->all();

        return view('livewire.home-component', [
            'hostels' => $hostels,
            'roomTypes' => $roomTypes,
        ]);
    }

    public function paginationView()
    {
        return 'livewire::bootstrap';
    }
}