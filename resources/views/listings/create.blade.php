@extends('layouts.app')

@section('title', 'Create Listing - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Create New Listing</h1>

            <form id="listingForm" action="/listings" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Title</label>
                    <input type="text" name="title" id="title" placeholder="e.g., Spacious 2-Bedroom Apartment in Osu"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Describe your property..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required></textarea>
                </div>

                <!-- Price -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Rent (GHS)</label>
                        <input type="number" name="price" id="price" placeholder="500"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deposit (GHS)</label>
                        <input type="number" name="deposit" id="deposit" placeholder="1000"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Property Details -->
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bedrooms</label>
                        <select name="bedrooms" id="bedrooms"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5+</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bathrooms</label>
                        <select name="bathrooms" id="bathrooms"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4+</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Area (sqft)</label>
                        <input type="number" name="area_sqft" id="area_sqft" placeholder="1000"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Property Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Type</label>
                    <select name="property_type" id="property_type"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="studio">Studio</option>
                        <option value="shared_room">Shared Room</option>
                        <option value="bungalow">Bungalow</option>
                    </select>
                </div>

                <!-- Neighborhood -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Neighborhood/District</label>
                    <input type="text" name="neighborhood" id="neighborhood" placeholder="e.g., Osu, Accra"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                </div>

                <!-- Amenities/Features -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="furnished" id="furnished" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Furnished</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="wifi" id="wifi" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">WiFi</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="parking" id="parking" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Parking</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="security" id="security" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Security</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="pool" id="pool" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Pool</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="gym" id="gym" class="w-4 h-4 text-red-600 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Gym</span>
                        </label>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photos (up to 3)</label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-red-500 dark:hover:border-red-400 transition bg-gray-50 dark:bg-gray-700"
                        onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, GIF up to 5MB each (max 3 photos)</p>
                    </div>
                    <input type="file" id="photoInput" name="photos" multiple accept="image/*" style="display: none;">

                    <!-- Photo Preview Grid -->
                    <div id="photoPreview" class="grid grid-cols-3 gap-3 mt-4"></div>
                    <div id="photoCount" class="text-sm text-gray-500 dark:text-gray-400 mt-3 text-center">
                        <span id="photoNum">0</span>/3 photos uploaded
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">
                        Create Listing
                    </button>
                    <a href="/dashboard/landlord" class="px-6 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-3 rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('photoPreview');
const photoNum = document.getElementById('photoNum');
let uploadedPhotos = [];

// Handle photo input change
photoInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files).slice(0, 3);
    uploadedPhotos = files;
    updatePhotoPreview();
});

// Handle drag and drop
photoPreview.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.target.closest('[id="photoPreview"]')?.classList.add('border-red-500');
});

document.querySelector('[onclick*="photoInput"]').addEventListener('dragover', (e) => {
    e.preventDefault();
    e.target.classList.add('border-red-500', 'bg-red-50');
});

document.querySelector('[onclick*="photoInput"]').addEventListener('dragleave', (e) => {
    e.preventDefault();
    e.target.classList.remove('border-red-500', 'bg-red-50');
});

document.querySelector('[onclick*="photoInput"]').addEventListener('drop', (e) => {
    e.preventDefault();
    e.target.classList.remove('border-red-500', 'bg-red-50');
    const files = Array.from(e.dataTransfer.files).slice(0, 3);
    uploadedPhotos = files;
    updatePhotoPreview();
});

// Update photo preview display
function updatePhotoPreview() {
    photoNum.textContent = uploadedPhotos.length;
    photoPreview.innerHTML = uploadedPhotos.map((file, idx) => {
        const url = URL.createObjectURL(file);
        const isPrimary = idx === 0;
        return `
            <div class="relative group">
                <img src="${url}" class="w-full h-40 object-cover rounded-lg shadow-md" alt="Photo ${idx + 1}">
                ${isPrimary ? '<div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Primary</div>' : ''}
                <button type="button" onclick="removePhoto(${idx})" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }).join('');
}

// Remove individual photo
function removePhoto(idx) {
    uploadedPhotos.splice(idx, 1);
    updatePhotoPreview();
}

// Handle form submission using standard form POST
document.getElementById('listingForm').addEventListener('submit', function(e) {
    // Add photos to FormData before submission
    const formElement = this;

    // Create a data transfer to add files to the form
    uploadedPhotos.forEach((photo, idx) => {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(photo);

        // We need to programmatically add the files to the actual input
        // Since FormData will be handled by the browser's default form submission
    });

    // For file inputs, we'll use a DataTransfer API workaround if needed
    // Otherwise the form will auto-submit with just the file input element
});
</script>
@endsection
