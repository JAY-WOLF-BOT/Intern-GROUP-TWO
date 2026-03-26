@extends('layouts.app')

@section('title', 'My Profile - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-12"></div>

            <!-- Profile Content -->
            <div class="px-6 py-8">
                <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-white">My Profile</h1>
                <p id="userEmail" class="text-gray-600 dark:text-gray-400"></p>

                @if ($message = Session::get('success'))
                    <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-green-800 dark:text-green-200"><i class="fas fa-check-circle mr-2"></i>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-red-800 dark:text-red-200"><i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                    </div>
                @endif

                <div class="grid md:grid-cols-3 gap-8 mt-8">
                    <!-- Profile Picture -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Profile Picture</h2>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center bg-gray-50 dark:bg-gray-700/50 hover:border-red-500 transition cursor-pointer" onclick="document.getElementById('profilePictureInput').click()">
                            <div id="profilePicturePreview" class="mb-4">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Click or drag to upload</p>
                            </div>
                            <img id="profilePictureImg" style="display: none;" class="w-full h-48 object-cover rounded-lg mb-4">
                        </div>
                        <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                        <form id="profilePictureForm" action="/profile/picture" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                        <div class="flex gap-2 mt-4">
                            <button type="button" onclick="uploadProfilePicture()" id="uploadBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition" style="display: none;">
                                <i class="fas fa-upload mr-2"></i> Upload
                            </button>
                            <button type="button" onclick="removeProfilePicture()" id="removeBtn" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 rounded-lg transition" style="display: none;">
                                <i class="fas fa-trash mr-2"></i> Remove
                            </button>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Personal Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <p id="userName" class="text-gray-900 dark:text-white"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                <p id="userPhone" class="text-gray-900 dark:text-white"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Type</label>
                                <p id="userRole" class="text-gray-900 dark:text-white capitalize"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Account Settings</h2>
                        <div class="space-y-4">
                            <button onclick="showPasswordModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                                <i class="fas fa-lock mr-2"></i> Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" style="display: none;" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-md w-full">
        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Change Password</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                <input type="password" id="currentPassword" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                <input type="password" id="newPassword" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                <input type="password" id="confirmPassword" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closePasswordModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-2 rounded-lg">Cancel</button>
            <button onclick="changePassword()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg">Change</button>
        </div>
    </div>
</div>

<script>
async function loadProfile() {
    // Check if user is logged in via session
    try {
        const response = await fetch('/profile', {
            credentials: 'include'
        });

        // For API fallback
        const token = localStorage.getItem('auth_token');
        if (token) {
            const apiResponse = await fetch('/api/v1/user/profile', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await apiResponse.json();
            if (data.success) {
                const user = data.data;
                document.getElementById('userName').textContent = user.name;
                document.getElementById('userEmail').textContent = user.email;
                document.getElementById('userPhone').textContent = user.phone_number;
                document.getElementById('userRole').textContent = user.role;

                if (user.profile_picture) {
                    const img = document.getElementById('profilePictureImg');
                    img.src = user.profile_picture;
                    img.style.display = 'block';
                    document.getElementById('profilePicturePreview').innerHTML = '';
                    document.getElementById('removeBtn').style.display = 'block';
                }
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Handle profile picture file input
document.getElementById('profilePictureInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('profilePictureImg');
            img.src = event.target.result;
            img.style.display = 'block';
            document.getElementById('profilePicturePreview').innerHTML = '';
            document.getElementById('uploadBtn').style.display = 'block';
            document.getElementById('removeBtn').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

// Upload profile picture
function uploadProfilePicture() {
    const fileInput = document.getElementById('profilePictureInput');
    const file = fileInput.files[0];

    if (!file) {
        alert('Please select a file');
        return;
    }

    // Get CSRF token from multiple sources for reliability
    function getCsrfToken() {
        // Try from hidden form input first
        let token = document.querySelector('#profilePictureForm input[name="_token"]')?.value;
        if (token) return token;

        // Try from meta tag
        token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) return token;

        // Fallback to any input with name="_token"
        token = document.querySelector('input[name="_token"]')?.value;
        return token || '';
    }

    const formData = new FormData();
    formData.append('profile_picture', file);
    formData.append('_token', getCsrfToken());

    fetch('/profile/picture', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => {
        if (response.ok || response.status === 302 || response.status === 301) {
            // Reload page to show success message or follow redirect
            window.location.reload();
        } else {
            response.text().then(text => {
                console.error('Upload failed:', response.status, text);
                alert('Failed to upload profile picture. Status: ' + response.status);
            });
        }
    })
    .catch(error => {
        console.error('Error uploading:', error);
        alert('Error uploading profile picture: ' + error.message);
    });
}

// Remove profile picture
function removeProfilePicture() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }

    // Get CSRF token from multiple sources for reliability
    function getCsrfToken() {
        // Try from hidden form input first
        let token = document.querySelector('#profilePictureForm input[name="_token"]')?.value;
        if (token) return token;

        // Try from meta tag
        token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) return token;

        // Fallback to any input with name="_token"
        token = document.querySelector('input[name="_token"]')?.value;
        return token || '';
    }

    fetch('/profile/picture', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => {
        if (response.ok || response.status === 302 || response.status === 301) {
            window.location.reload();
        } else {
            response.text().then(text => {
                console.error('Delete failed:', response.status, text);
                alert('Failed to remove profile picture. Status: ' + response.status);
            });
        }
    })
    .catch(error => {
        console.error('Error removing:', error);
        alert('Error removing profile picture: ' + error.message);
    });
}

// Drag and drop
document.querySelector('.border-dashed').addEventListener('dragover', (e) => {
    e.preventDefault();
    e.target.classList.add('border-red-500', 'bg-red-50');
});

document.querySelector('.border-dashed').addEventListener('dragleave', (e) => {
    e.target.classList.remove('border-red-500', 'bg-red-50');
});

document.querySelector('.border-dashed').addEventListener('drop', (e) => {
    e.preventDefault();
    e.target.classList.remove('border-red-500', 'bg-red-50');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('profilePictureInput').files = files;
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        document.getElementById('profilePictureInput').dispatchEvent(event);
    }
});

function showPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}

async function changePassword() {
    const token = localStorage.getItem('auth_token');
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }

    try {
        const response = await fetch('/api/v1/user/password/change', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ password: newPassword })
        });
        const data = await response.json();
        if (data.success) {
            alert('Password changed successfully!');
            closePasswordModal();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

window.addEventListener('DOMContentLoaded', loadProfile);
</script>
@endsection
