@extends('layouts.app')

@section('title', 'Create Listing - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Create New Listing</h1>

            <form id="listingForm" action="/listings" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Property Details Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Property Details</h2>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Title</label>
                        <input type="text" name="title" id="title" placeholder="e.g., Spacious 2-Bedroom Apartment in Osu"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Description</label>
                        <textarea name="description" id="description" rows="4" placeholder="Describe your property, including bedrooms, amenities, etc."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Rent</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">₵</span>
                            <input type="number" name="price" id="price" placeholder="500"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                    </div>

                    <!-- Property Type and Category -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Type</label>
                            <select name="property_type" id="property_type"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Type</option>
                                <option value="apartment">Apartment</option>
                                <option value="house">Standalone House</option>
                                <option value="studio">Studio</option>
                                <option value="shared_room">Shared Room</option>
                                <option value="bungalow">Bungalow</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select name="category" id="category"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Category</option>
                                <option value="student_housing">Student Housing</option>
                                <option value="luxury">Luxury</option>
                                <option value="commercial">Commercial</option>
                                <option value="family">Family</option>
                                <option value="budget">Budget</option>
                                <option value="short_term">Short Term</option>
                            </select>
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

                    <!-- Deposit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deposit (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">₵</span>
                            <input type="number" name="deposit" id="deposit" placeholder="1000"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Location</h2>

                    <!-- Neighborhood -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Neighborhood/District</label>
                        <input type="text" name="neighborhood" id="neighborhood" placeholder="e.g., Osu, Accra"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <!-- Google Map -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property Location (Click on map to set location)</label>
                        <div id="map" class="w-full h-64 rounded-lg border border-gray-300 dark:border-gray-600"></div>
                        <input type="hidden" name="location_lat" id="location_lat">
                        <input type="hidden" name="location_long" id="location_long">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Click on the map to set the exact location of your property</p>
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Amenities</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select all that apply</label>
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
                </div>

                <!-- Photos Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Photos (Exactly 3 Required)</h2>
                    <div>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-red-500 dark:hover:border-red-400 transition bg-gray-50 dark:bg-gray-700"
                            onclick="document.getElementById('photoInput').click()" id="photoDropZone">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Click to upload or drag and drop photos</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, GIF up to 5MB each (exactly 3 photos required)</p>
                        </div>
                        <input type="file" id="photoInput" name="photos[]" multiple accept="image/*" style="display: none;" onchange="handlePhotoSelect(event)">

                        <!-- Photo Preview Grid -->
                        <div id="photoPreview" class="grid grid-cols-3 gap-3 mt-4"></div>
                        <div id="photoCount" class="text-sm text-gray-500 dark:text-gray-400 mt-3 text-center">
                            <span id="photoNum">0</span>/3 photos uploaded
                        </div>
                        <div id="photoError" class="text-sm text-red-600 dark:text-red-400 mt-2 text-center hidden">
                            Please upload exactly 3 photos
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit" id="submitBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
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
let uploadedPhotos = [];
const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('photoPreview');
const photoNum = document.getElementById('photoNum');
const photoError = document.getElementById('photoError');
const submitBtn = document.getElementById('submitBtn');
const photoDropZone = document.getElementById('photoDropZone');

// Initialize Google Map
let map;
let marker;

function initMap() {
    // Default to Accra coordinates
    const accra = { lat: 5.6037, lng: -0.1870 };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: accra,
        styles: [
            {
                "featureType": "all",
                "elementType": "geometry.fill",
                "stylers": [{"weight": "2.00"}]
            },
            {
                "featureType": "all",
                "elementType": "geometry.stroke",
                "stylers": [{"color": "#9c9c9c"}]
            },
            {
                "featureType": "all",
                "elementType": "labels.text",
                "stylers": [{"visibility": "on"}]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{"color": "#f2f2f2"}]
            },
            {
                "featureType": "landscape",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [{"saturation": -100}, {"lightness": 45}]
            },
            {
                "featureType": "road",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#eeeeee"}]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{"color": "#7b7b7b"}]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [{"color": "#ffffff"}]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [{"visibility": "simplified"}]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.icon",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]
            },
            {
                "featureType": "water",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#c8d7d4"}]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{"color": "#070707"}]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [{"color": "#ffffff"}]
            }
        ]
    });

    // Add click listener to map
    map.addListener('click', function(event) {
        placeMarker(event.latLng);
    });

    // Add initial marker
    placeMarker(accra);
}

function placeMarker(location) {
    if (marker) {
        marker.setPosition(location);
    } else {
        marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true
        });

        // Add drag listener
        marker.addListener('dragend', function(event) {
            updateLocationFields(event.latLng);
        });
    }

    updateLocationFields(location);
}

function updateLocationFields(location) {
    document.getElementById('location_lat').value = location.lat();
    document.getElementById('location_long').value = location.lng();
}

// Handle photo selection
function handlePhotoSelect(event) {
    const files = Array.from(event.target.files);
    if (files.length !== 3) {
        photoError.textContent = 'Please select exactly 3 photos';
        photoError.classList.remove('hidden');
        uploadedPhotos = [];
        updatePhotoPreview();
        return;
    }

    // Validate file types and sizes
    const validFiles = files.filter(file => {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        return validTypes.includes(file.type) && file.size <= maxSize;
    });

    if (validFiles.length !== 3) {
        photoError.textContent = 'All files must be images (JPEG, PNG, GIF) under 5MB';
        photoError.classList.remove('hidden');
        uploadedPhotos = [];
        updatePhotoPreview();
        return;
    }

    uploadedPhotos = validFiles;
    photoError.classList.add('hidden');
    updatePhotoPreview();
}

// Handle drag and drop
photoDropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    photoDropZone.classList.add('border-red-500', 'bg-red-50');
});

photoDropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    photoDropZone.classList.remove('border-red-500', 'bg-red-50');
});

photoDropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    photoDropZone.classList.remove('border-red-500', 'bg-red-50');
    const files = Array.from(e.dataTransfer.files);
    if (files.length === 3) {
        // Create a fake event to reuse the handler
        const fakeEvent = { target: { files: files } };
        handlePhotoSelect(fakeEvent);
    } else {
        photoError.textContent = 'Please drop exactly 3 photos';
        photoError.classList.remove('hidden');
    }
});

// Update photo preview display
function updatePhotoPreview() {
    photoNum.textContent = uploadedPhotos.length;
    photoPreview.innerHTML = uploadedPhotos.map((file, idx) => {
        const url = URL.createObjectURL(file);
        const isPrimary = idx === 0;
        return `
            <div class="relative group">
                <img src="${url}" class="w-full h-32 object-cover rounded-lg shadow-md" alt="Photo ${idx + 1}">
                ${isPrimary ? '<div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Primary</div>' : ''}
                <button type="button" onclick="removePhoto(${idx})" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }).join('');

    // Enable/disable submit button based on photo count
    submitBtn.disabled = uploadedPhotos.length !== 3;
}

// Remove individual photo
function removePhoto(idx) {
    uploadedPhotos.splice(idx, 1);
    updatePhotoPreview();
    if (uploadedPhotos.length !== 3) {
        photoError.textContent = 'Please upload exactly 3 photos';
        photoError.classList.remove('hidden');
    }
}

// Form validation before submit
document.getElementById('listingForm').addEventListener('submit', function(e) {
    if (uploadedPhotos.length !== 3) {
        e.preventDefault();
        photoError.textContent = 'Please upload exactly 3 photos before submitting';
        photoError.classList.remove('hidden');
        return false;
    }
});

// Load Google Maps API
window.initMap = initMap;
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"></script>

@endsection
