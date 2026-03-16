@extends('layouts.admin')

@section('title', 'New Breakdown')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Report New Breakdown</h1>
        <p class="mt-2 text-gray-600">Enter the details of the incident.</p>
    </div>

    <form action="{{ route('admin.breakdowns.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Vehicle *</label>
                <select name="vehicle_id" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select vehicle</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->plate_number }} — {{ $v->make }} {{ $v->model }}</option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Driver</label>
                <select name="driver_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">—</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('driver_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Location</label>
                <input type="text" name="location" value="{{ old('location') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('location') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Occurred At *</label>
                <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at') }}" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('occurred_at') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Severity *</label>
                <select name="severity" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="minor">Minor</option>
                    <option value="moderate" selected>Moderate</option>
                    <option value="major">Major</option>
                    <option value="critical">Critical</option>
                </select>
                @error('severity') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
            <textarea name="description" rows="4" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Estimated Cost (UGX)</label>
            <input type="number" name="estimated_cost" step="1" min="0" value="{{ old('estimated_cost') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('estimated_cost') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Photo upload section (your latest version kept) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Photos</label>

            <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="hidden">

            <div id="drop-area" class="mt-2 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 bg-gray-50 transition">
                <p class="text-base text-gray-600">
                    <span class="font-medium text-indigo-600">Click to upload</span> or drag & drop
                </p>
                <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG, GIF (max 5MB per file)</p>
            </div>

            <div id="preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-6"></div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition">
                Submit Report
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- your JavaScript for drag-drop + preview remains the same -->
<script>

const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('photos');
const preview = document.getElementById('preview');

let uploadedFiles = [];

/* click upload */
dropArea.addEventListener('click', () => fileInput.click());

/* file select */
fileInput.addEventListener('change', (e) => {

    uploadedFiles = [...uploadedFiles, ...Array.from(e.target.files)];

    updateFileList();
    renderPreview();

});

/* drag events */
['dragenter','dragover'].forEach(eventName => {

    dropArea.addEventListener(eventName,(e)=>{
        e.preventDefault();
        dropArea.classList.add('border-indigo-500');
    });

});

['dragleave','drop'].forEach(eventName => {

    dropArea.addEventListener(eventName,(e)=>{
        e.preventDefault();
        dropArea.classList.remove('border-indigo-500');
    });

});

/* drop upload */
dropArea.addEventListener('drop',(e)=>{

    const files = Array.from(e.dataTransfer.files);

    uploadedFiles = [...uploadedFiles, ...files];

    updateFileList();
    renderPreview();

});

/* update input file list */
function updateFileList(){

    const dt = new DataTransfer();

    uploadedFiles.forEach(file => dt.items.add(file));

    fileInput.files = dt.files;

}

/* preview images */
function renderPreview(){

    preview.innerHTML = '';

    uploadedFiles.forEach((file,index)=>{

        const reader = new FileReader();

        reader.onload = function(e){

            const div = document.createElement('div');
            div.className = "relative";

            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow">
                <button type="button"
                    class="absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full text-xs"
                    onclick="removeFile(${index})">
                    ×
                </button>
            `;

            preview.appendChild(div);

        };

        reader.readAsDataURL(file);

    });

}

/* remove file */
function removeFile(index){

    uploadedFiles.splice(index,1);

    updateFileList();
    renderPreview();

}

</script>
@endpush
@endsection


