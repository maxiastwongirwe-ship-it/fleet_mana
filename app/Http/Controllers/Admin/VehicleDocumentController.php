<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentController extends Controller
{
   
   public function __construct()
    {

    }

   
    /**
     * Display ALL documents (with filter option for one vehicle)
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::orderBy('plate_number')->get(); // for dropdown filter

        $query = VehicleDocument::with('vehicle')->orderByDesc('expiry_date');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $documents = $query->paginate(20);

        return view('admin.vehicles.documents.index', compact('documents', 'vehicles'));
    }

    /**
     * Form to create new document (select vehicle from dropdown)
     */
    public function create()
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();

        return view('admin.vehicles.documents.create', compact('vehicles'));
    }

    /**
     * Store new document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'        => ['required', 'exists:vehicles,id'],
            'document_type'     => ['required', 'in:insurance,third_party,inspection,permit,roadworthy,license,other'],
            'document_number'   => ['nullable', 'string', 'max:100'],
            'issue_date'        => ['nullable', 'date'],
            'expiry_date'       => ['nullable', 'date', 'after_or_equal:issue_date'],
            'file'              => ['required', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:5120'],
            'is_valid'          => ['boolean'],
            'notes'             => ['nullable', 'string'],
        ]);

        $data = $validated;
        $data['uploaded_by'] = auth()->id();

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $data['file_path'] = $request->file('file')->store('vehicle-documents', 'public');
        }

        VehicleDocument::create($data);

        return redirect()->route('admin.vehicledocuments.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show single document
     */
    public function show(VehicleDocument $vehicledocument)
    {
        $vehicledocument->load('vehicle', 'uploadedBy');

        return view('admin.vehicles.documents.show', compact('vehicledocument'));
    }

    /**
     * Edit document
     */
    public function edit(VehicleDocument $vehicledocument)
    {
        $vehicledocument->load('vehicle');
        $vehicles = Vehicle::orderBy('plate_number')->get();

        return view('admin.vehicles.documents.edit', compact('vehicledocument', 'vehicles'));
    }

    /**
     * Update document
     */
    public function update(Request $request, VehicleDocument $vehicledocument)
    {
        $validated = $request->validate([
            'vehicle_id'        => ['required', 'exists:vehicles,id'],
            'document_type'     => ['required', 'in:insurance,third_party,inspection,permit,roadworthy,license,other'],
            'document_number'   => ['nullable', 'string', 'max:100'],
            'issue_date'        => ['nullable', 'date'],
            'expiry_date'       => ['nullable', 'date', 'after_or_equal:issue_date'],
            'file'              => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:5120'],
            'is_valid'          => ['boolean'],
            'notes'             => ['nullable', 'string'],
        ]);

        $data = $validated;

        if ($request->hasFile('file')) {
            if ($vehicledocument->file_path) {
                Storage::disk('public')->delete($vehicledocument->file_path);
            }
            $data['file_path'] = $request->file('file')->store('vehicle-documents', 'public');
        }

        $vehicledocument->update($data);

        return redirect()->route('admin.vehicledocuments.index')
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Delete document
     */
    public function destroy(VehicleDocument $vehicledocument)
    {
        if ($vehicledocument->file_path) {
            Storage::disk('public')->delete($vehicledocument->file_path);
        }

        $vehicledocument->delete();

        return redirect()->route('admin.vehicledocuments.index')
            ->with('success', 'Document deleted successfully.');
    }
    /**
 * Display documents for a specific vehicle (filtered by vehicle ID)
 */
public function vehicleDocuments(Request $request, Vehicle $vehicle)
{
    $documents = $vehicle->documents()
        ->orderByDesc('expiry_date')
        ->paginate(15);

    return view('admin.vehicles.documents.vehicle-documents', compact('vehicle', 'documents'));
}
}
