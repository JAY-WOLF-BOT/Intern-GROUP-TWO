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
                <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>

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

                @if ($errors->any())
                    <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <ul class="text-red-800 dark:text-red-200">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-8 grid md:grid-cols-3 gap-8">
                    <!-- Profile Picture -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Profile Picture</h2>
                        <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center bg-gray-50 dark:bg-gray-700/50 hover:border-red-500 transition cursor-pointer" onclick="document.getElementById('profilePictureInput').click()">
                                <div id="profilePicturePreview" class="mb-4">
                                    @if(auth()->user()->profile_picture)
                                        <img id="profilePictureImg" src="{{ asset('storage/' . auth()->user()->profile_picture) }}" class="w-full h-48 object-cover rounded-lg mb-4">
                                    @else
                                        <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">Click to upload</p>
                                    @endif
                                </div>
                            </div>
                            <input type="file" id="profilePictureInput" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                                <i class="fas fa-upload mr-2"></i> Save Picture
                            </button>
                        </form>

                        <!-- Remove Button - Separate Form -->
                        @if(auth()->user()->profile_picture)
                            <form id="removeAvatarForm" action="{{ route('profile.picture.delete') }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 rounded-lg transition">
                                    <i class="fas fa-trash mr-2"></i> Remove Picture
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Personal Information -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Personal Information</h2>
                        <form action="{{ route('profile.info') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                    <input type="text" name="phone_number" value="{{ auth()->user()->phone_number }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Type</label>
                                    <input type="text" value="{{ ucfirst(auth()->user()->role) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white bg-gray-100 dark:bg-gray-600" readonly>
                                </div>
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                                    <i class="fas fa-save mr-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Settings -->
                    <div class="md:col-span-1">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Account Settings</h2>
                        <div class="space-y-4">
                            <button type="button" onclick="showPasswordModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
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
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                    <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-2 rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg">Change Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePicturePreview');
            preview.innerHTML = `<img id="profilePictureImg" src="${e.target.result}" class="w-full h-48 object-cover rounded-lg mb-4">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePasswordModal();
    }
});
</script>
@endsection