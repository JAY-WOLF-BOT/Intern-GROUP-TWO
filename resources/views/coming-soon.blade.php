@extends('layouts.app')

@section('title', $feature . ' - Coming Soon - Accra Housing')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <i class="fas fa-tools text-6xl text-gray-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $feature }} Feature</h1>
        <p class="text-gray-600 mb-6">This feature is currently under development and will be available soon.</p>
        <div class="space-y-3">
            @if(auth()->user()->role === 'landlord')
                <a href="{{ route('dashboard.landlord') }}" class="block w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                    Back to Dashboard
                </a>
            @else
                <a href="{{ route('dashboard.tenant') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Back to Dashboard
                </a>
            @endif
        </div>
    </div>
</div>
@endsection