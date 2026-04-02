<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Client;
use App\Models\DocumentType;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Services\ImageService;
use App\Services\DocumentService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected ImageService $imageService;
    protected DocumentService $documentService;

    public function __construct(ImageService $imageService, DocumentService $documentService)
    {
        $this->imageService = $imageService;
        $this->documentService = $documentService;
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = Property::with(['agent', 'client', 'primaryImage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if (!auth()->user()->isAdmin()) {
            $query->where(function($q) {
                $q->where('agent_id', auth()->id())
                  ->orWhere('visibility', 'public');
            });
        }

        $properties = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only(['status', 'city', 'search']),
            'cities' => Property::select('city')->distinct()->pluck('city'),
            'statuses' => ['draft', 'pending_review', 'approved', 'active', 'sold', 'archived']
        ]);
    }

    public function create()
    {
        $this->authorize('create', Property::class);

        return Inertia::render('Properties/Create', [
            'clients' => Client::select('id', 'name', 'phone')->get(),
            'documentTypes' => DocumentType::all(),
            'cities' => config('properties.cities', []),
            'amenities' => config('properties.amenities', [])
        ]);
    }

    public function store(StorePropertyRequest $request)
    {
        $property = Property::create($request->validated());

        activity()
            ->performedOn($property)
            ->withProperties(['data' => $request->all()])
            ->log('created_property');

        return redirect()->route('properties.show', $property)
            ->with('success', 'تم إنشاء العقار بنجاح');
    }

    public function show(Property $property)
    {
        $this->authorize('view', $property);

        $property->load(['agent', 'client', 'images', 'documents.type', 'deals']);
        $property->incrementViews();

        return Inertia::render('Properties/Show', [
            'property' => $property,
            'documentTypes' => DocumentType::all()
        ]);
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);

        return Inertia::render('Properties/Edit', [
            'property' => $property,
            'clients' => Client::select('id', 'name', 'phone')->get(),
            'documentTypes' => DocumentType::all()
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $property->update($request->validated());

        activity()
            ->performedOn($property)
            ->withProperties([
                'old' => $property->getOriginal(),
                'new' => $request->validated()
            ])
            ->log('updated_property');

        return redirect()->route('properties.show', $property)
            ->with('success', 'تم تحديث العقار بنجاح');
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        $property->delete();

        activity()
            ->performedOn($property)
            ->log('deleted_property');

        return redirect()->route('properties.index')
            ->with('success', 'تم حذف العقار بنجاح');
    }

    public function uploadImages(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $index => $image) {
            $result = $this->imageService->upload(
                $image,
                "properties/{$property->id}/images"
            );

            if ($result['success']) {
                $property->images()->create([
                    'path' => $result['paths']['large'] ?? $result['paths']['original'],
                    'thumbnail_path' => $result['paths']['thumbnail'] ?? null,
                    'is_primary' => $index === 0,
                    'sort_order' => $property->images()->count() + $index + 1
                ]);

                $uploadedImages[] = $result;
            }
        }

        return back()->with('success', "تم رفع " . count($uploadedImages) . " صورة بنجاح");
    }

    public function deleteImage(Property $property, $imageId)
    {
        $this->authorize('update', $property);

        $image = $property->images()->findOrFail($imageId);
        
        $this->imageService->delete([
            $image->path,
            $image->thumbnail_path
        ]);

        $image->delete();

        return back()->with('success', 'تم حذف الصورة');
    }
}