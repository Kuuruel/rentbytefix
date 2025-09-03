@extends('layout.layout')

@php
    $subTitle = 'Tenant Profile';
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-950 z-[0]">
    <div class="container mx-8 px-2 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Profile Card -->
            <div class="col-span-12 lg:col-span-4">
                <div class="relative group h-full">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                    <div class="relative h-full bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all duration-500 hover:scale-105">

                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-blue-950/20 dark:via-purple-950/20 dark:to-pink-950/20"></div>
                        <div class="absolute inset-0 opacity-30 dark:opacity-10" style="background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.15) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(147, 51, 234, 0.15) 0%, transparent 50%);"></div>

                        <div class="relative p-8 pt-8">
                            <div class="text-center">
                                <!-- Avatar -->
                                <div class="relative inline-block mb-6 p-6">
                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-md opacity-50 animate-pulse"></div>
                                    <div id="profileCardAvatar" class="relative w-52 h-52 mx-auto rounded-full overflow-hidden border-4 border-white dark:border-gray-700 shadow-2xl bg-white dark:bg-gray-800 transform transition-all duration-300 hover:scale-110" style="min-width: 208px; min-height: 208px; max-width: 208px; max-height: 208px;">
                                        @if($tenant->avatar)
                                            <img src="{{ asset('assets/images/tenants/' . $tenant->avatar) }}" 
                                                alt="{{ $tenant->name }}" 
                                                class="absolute inset-0 w-full h-full object-cover object-center rounded-full"
                                                style="width: 208px !important; height: 208px !important; aspect-ratio: 1/1;"
                                                data-fallback-img>
                                            <div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-full hidden">
                                                <span class="text-4xl font-bold text-white">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-full">
                                                <span class="text-4xl font-bold text-white">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Name and Email -->
                                <h2 class="text-2xl font-bold mb-3 text-gray-800 dark:text-white tracking-tight bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $tenant->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-300 mb-4 break-words max-w-xs mx-auto leading-relaxed font-medium">{{ $tenant->email }}</p>

                                <!-- Country Badge -->
                                <div class="mb-4">
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $tenant->country }}
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="inline-flex items-center px-6 py-3 rounded-full text-sm font-semibold shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl
                                    @if($tenant->status === 'Active')
                                        bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    @else
                                        bg-gradient-to-r from-gray-400 to-gray-600 text-white
                                    @endif">
                                    <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $tenant->status }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Forms -->
            <div class="col-span-12 lg:col-span-8">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-20 group-hover:opacity-30 transition duration-500"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden px-4">

                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-1">
                            <div class="bg-white dark:bg-gray-800 rounded-t-2xl">
                                <div class="p-8">
                                    <!-- Tabs -->
                                    <div class="flex border-b-2 border-gray-200 dark:border-gray-700 mb-8" id="profile-tabs" role="tablist">
                                        <button class="tab-button flex-1 px-6 py-4 text-sm font-semibold border-b-3 border-transparent text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300 rounded-t-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 relative overflow-hidden group" data-tab="edit-profile" role="tab" aria-controls="edit-profile" aria-selected="true">
                                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            <div class="relative flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit Profile
                                            </div>
                                        </button>
                                        <button class="tab-button flex-1 px-6 py-4 text-sm font-semibold border-b-3 border-transparent text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300 rounded-t-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 relative overflow-hidden group" data-tab="change-password" role="tab" aria-controls="change-password" aria-selected="false">
                                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            <div class="relative flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Change Password
                                            </div>
                                        </button>
                                    </div>

                                    <!-- Edit Profile Tab -->
                                    <div id="edit-profile" class="tab-content" role="tabpanel" aria-labelledby="edit-profile-tab">
                                        <!-- Avatar Upload Section -->
                                        <div class="mb-6">
                                            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center">
                                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl mr-3 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                Profile Avatar
                                            </h3>
                                            <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                                                <div class="relative group">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl blur opacity-50 group-hover:opacity-75 transition duration-300"></div>
                                                    <div id="imagePreview" 
                                                         class="relative w-32 h-32 rounded-2xl border-4 border-white dark:border-gray-700 shadow-2xl bg-center bg-cover overflow-hidden transition-all duration-300 transform group-hover:scale-105 bg-no-repeat"
                                                         @if($tenant->avatar)
                                                             style="background-image: url('{{ asset('assets/images/tenants/' . $tenant->avatar) }}'); background-size: cover; background-position: center;"
                                                         @endif>
                                                        @if(!$tenant->avatar)
                                                            <div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-2xl">
                                                                <span class="text-3xl font-bold text-white">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex-1 w-full md:w-auto">
                                                    <label for="avatar" 
                                                        class="block cursor-pointer bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 p-6 rounded-2xl border-2 border-dashed border-blue-300 dark:border-blue-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-300 opacity-50 cursor-not-allowed group">
                                                        <div class="text-center">
                                                            <svg class="mx-auto h-12 w-12 text-blue-500 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                            </svg>
                                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Upload new avatar</p>
                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">JPG, PNG or GIF format</p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-500">Maximum size: 2MB • Recommended: 400x400px</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <form id="edit-profile-form" action="{{ route('tenant.updateProfile', $tenant->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="file" id="avatar" name="avatar" accept=".png,.jpg,.jpeg,.gif" class="hidden" disabled>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <!-- Name Field -->
                                                <div class="space-y-2">
                                                    <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        Full Name <span class="text-red-500 ml-1">*</span>
                                                    </label>
                                                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" 
                                                           class="w-full mt-2 px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                           placeholder="Enter your full name" required readonly disabled>
                                                </div>

                                                <!-- Email Field -->
                                                <div class="space-y-2">
                                                    <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Email Address <span class="text-red-500 ml-1">*</span>
                                                    </label>
                                                    <input type="email" id="email" name="email" value="{{ old('email', $tenant->email) }}" 
                                                           class="w-full mt-2 px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                           placeholder="Enter your email address" required readonly disabled>
                                                </div>

                                                <!-- Country Field -->
                                                <div class="space-y-2">
                                                    <label for="country" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Country <span class="text-red-500 ml-1">*</span>
                                                    </label>
                                                    <input type="text" id="country" name="country" value="{{ old('country', $tenant->country) }}" 
                                                           class="w-full mt-2 px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                           placeholder="Enter your country" required readonly disabled>
                                                </div>

                                                <!-- Status Field -->
                                                <div class="space-y-2">
                                                    <label for="status" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Status
                                                    </label>
                                                    <select id="status" name="status" 
                                                            class="w-full mt-2 px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium" 
                                                            disabled>
                                                        <option value="Active" {{ $tenant->status === 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="Inactive" {{ $tenant->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>

                                                <!-- Note Field (Full Width) -->
                                                <div class="space-y-2 md:col-span-2">
                                                    <label for="note" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Notes
                                                    </label>
                                                    <textarea id="note" name="note" rows="4"
                                                              class="w-full mt-2 px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                              placeholder="Add any additional notes..." readonly disabled>{{ old('note', $tenant->note) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Form Buttons -->
                                            <div id="form-buttons" class="flex flex-col sm:flex-row items-center justify-end space-y-4 sm:space-y-0 sm:space-x-4 mt-10 hidden">
                                                <button type="button" id="cancel-edit" 
                                                        class="w-full sm:w-auto px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-500/20 transition-all duration-300 font-semibold transform hover:scale-105">
                                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Cancel
                                                </button>
                                                <button type="submit" 
                                                        class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 font-semibold shadow-xl hover:shadow-2xl transform hover:scale-105 flex items-center justify-center">
                                                    <span class="submit-text flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Save Changes
                                                    </span>
                                                    <span class="submit-loading hidden flex items-center">
                                                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                        </svg>
                                                        Saving...
                                                    </span>
                                                </button>
                                            </div>
                                        </form>

                                        <!-- Edit Button -->
                                        <div id="edit-button-container" class="flex justify-end mt-10">
                                            <button type="button" id="enable-edit" 
                                                    class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 font-bold shadow-xl hover:shadow-2xl transform hover:scale-105 relative overflow-hidden group">
                                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                <span class="relative flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit Profile
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Change Password Tab -->
                                    <div id="change-password" class="tab-content hidden" role="tabpanel" aria-labelledby="change-password-tab">
                                        <div class="mx-auto">
                                            <div class="text-center mb-10">
                                                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full mb-6 shadow-xl relative">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full blur opacity-30 animate-pulse"></div>
                                                    <svg class="w-12 h-12 text-blue-600 dark:text-blue-400 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Change Password</h3>
                                                <p class="text-gray-600 dark:text-gray-400 text-lg">Update your password to keep your account secure</p>
                                            </div>

                                            <form id="password-form" action="{{ route('tenant.updatePassword', $tenant->id) }}" method="POST">
                                                @csrf
                                                <div class="space-y-8 px-10">
                                                    <!-- New Password -->
                                                    <div class="space-y-2">
                                                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                            <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                            New Password <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <div class="relative">
                                                            <input type="password" id="password" name="password" 
                                                                   class="w-full mt-2 px-4 py-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                                   placeholder="Enter new password" required disabled minlength="6">
                                                            <button type="button" 
                                                                    class="toggle-password absolute mt-2 right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 dark:text-gray-500 dark:hover:text-blue-400 p-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200" 
                                                                    data-target="password">
                                                                <iconify-icon icon="ph:eye" style="font-size: 20px;"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Confirm Password -->
                                                    <div class="space-y-2">
                                                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                                            <svg class="w-4 h-4 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                            Confirm Password <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <div class="relative">
                                                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                                                   class="w-full mt-2 px-4 py-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed font-medium placeholder-gray-400 dark:placeholder-gray-500" 
                                                                   placeholder="Confirm new password" required disabled minlength="6">
                                                            <button type="button" 
                                                                    class="toggle-password absolute mt-2 right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 dark:text-gray-500 dark:hover:text-blue-400 p-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200" 
                                                                    data-target="password_confirmation">
                                                                <iconify-icon icon="ph:eye" style="font-size: 20px;"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Password Form Buttons -->
                                                <div id="password-buttons" class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4 mt-10 hidden">
                                                    <button type="button" id="cancel-password" 
                                                            class="w-full sm:w-auto px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-500/20 transition-all duration-300 font-semibold transform hover:scale-105">
                                                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Cancel
                                                    </button>
                                                    <button type="submit" 
                                                            class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 font-semibold shadow-xl hover:shadow-2xl transform hover:scale-105 flex items-center justify-center">
                                                        <span class="submit-text flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                            </svg>
                                                            Update Password
                                                        </span>
                                                        <span class="submit-loading hidden flex items-center">
                                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                            </svg>
                                                            Updating...
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>

                                            <!-- Change Password Button -->
                                            <div id="password-button-container" class="flex justify-center mt-6 mb-6">
                                                <button type="button" id="enable-password-edit" 
                                                        class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 font-bold shadow-xl hover:shadow-2xl transform hover:scale-105 relative overflow-hidden group">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    <span class="relative flex items-center">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                        </svg>
                                                        Change Password
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>

<script>
  window.TenantProfile = {
    name: @json($tenant->name),
    email: @json($tenant->email),
    avatar: @json($tenant->avatar ? asset('assets/images/tenants/' . $tenant->avatar) : ''),
    initial: @json(strtoupper(substr($tenant->name, 0, 1))),
    country: @json($tenant->country),
    status: @json($tenant->status),
    note: @json($tenant->note)
  };
</script>

<script>
  window.Flash = {
    success: @json(session('success') ?? null),
    error: @json(session('error') ?? null)
  };
</script>

<script src="{{ asset('assets/js/tenant-profile.js') }}"></script>

<style>
.tab-button[aria-selected="true"] {
    @apply text-blue-600 dark:text-blue-400 border-b-blue-600 dark:border-b-blue-400 bg-gradient-to-b from-blue-100/50 to-transparent dark:from-blue-900/30;
}

.border-b-3 {
    border-bottom-width: 3px;
}

.notification-toast {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

@keyframes tilt {
    0%, 50%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(1deg);
    }
    75% {
        transform: rotate(-1deg);
    }
}

.animate-tilt {
    animation: tilt 10s infinite linear;
}

.dark ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.dark ::-webkit-scrollbar-track {
    background: #374151;
    border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
}

.focus-ring:focus {
    @apply ring-4 ring-blue-500/20 ring-offset-2 ring-offset-white dark:ring-offset-gray-800;
}

.gradient-text {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-gradient {
    position: relative;
    overflow: hidden;
}

.btn-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-gradient:hover::before {
    left: 100%;
}

@media (max-width: 640px) {
    .notification-toast {
        width: calc(100vw - 32px) !important;
        right: 16px !important;
        top: 16px !important;
    }
    
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

.form-input:focus {
    @apply ring-4 ring-blue-500/20 border-blue-500 scale-105;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-input:disabled {
    @apply bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed;
}

@keyframes shimmer {
    0% {
        background-position: -468px 0;
    }
    100% {
        background-position: 468px 0;
    }
}

.shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 400% 100%;
    animation: shimmer 1.2s ease-in-out infinite;
}

.dark .shimmer {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 400% 100%;
}

* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.focus-visible {
    @apply focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-500/20 focus-visible:ring-offset-2;
}
</style>

@endsection

