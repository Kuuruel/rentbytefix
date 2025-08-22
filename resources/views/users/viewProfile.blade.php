@extends('layout.layout')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-4">
        <div class="relative border border-neutral-200 dark:border-neutral-600 rounded-2xl overflow-hidden bg-white dark:bg-neutral-800 h-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-neutral-200/25 dark:hover:shadow-neutral-900/25">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30"></div>
            <div class="relative pb-6 px-6 pt-20">
                <div class="text-center border-b border-neutral-200 dark:border-neutral-600 pb-6">
                    <div class="relative inline-block mb-4">
                        @if($user->img)
                            <div class="relative w-20 h-20 mx-auto rounded-full overflow-hidden border-4 border-white dark:border-neutral-700 shadow-xl bg-white">
                                <img src="{{ asset('assets/images/super-admin/' . $user->img) }}"
                                    alt="{{ $user->name }}"
                                    class="w-full h-full object-cover"
                                    data-fallback-img
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 items-center justify-center text-white font-bold text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-full mx-auto border-4 border-white dark:border-neutral-700 shadow-xl bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>

                    <h6 class="text-xl font-bold mb-2 text-neutral-800 dark:text-neutral-100 tracking-tight">{{ $user->name }}</h6>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4 break-words max-w-xs mx-auto leading-relaxed">{{ $user->email }}</p>

                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg transform transition-all duration-200 hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                        </svg>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-8">
        <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-600 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-1">
                <div class="bg-white dark:bg-neutral-800 rounded-xl">
                    <div class="p-6">
                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <ul class="flex border-b border-neutral-200 dark:border-neutral-600 mb-6" id="profile-tabs" role="tablist">
                            <li role="presentation" class="flex-1">
                                <button class="tab-button w-full px-6 py-4 text-sm font-semibold border-b-3 border-transparent text-neutral-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 rounded-t-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/50" data-tab="edit-profile" role="tab" aria-controls="edit-profile" aria-selected="true">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Profile
                                </button>
                            </li>
                            <li role="presentation" class="flex-1">
                                <button class="tab-button w-full px-6 py-4 text-sm font-semibold border-b-3 border-transparent text-neutral-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 rounded-t-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/50" data-tab="change-password" role="tab" aria-controls="change-password" aria-selected="false">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Change Password
                                </button>
                            </li>
                        </ul>

                        <div id="edit-profile" class="tab-content">
                            <div class="mb-8">
                                <h6 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-6 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Profile Image
                                </h6>
<div class="flex flex-row items-center space-x-8">
    <div class="relative group">
        <div id="imagePreview" 
             class="w-28 h-28 rounded-xl border-4 border-white dark:border-neutral-700 shadow-xl bg-center bg-cover overflow-hidden transition-all duration-300 bg-no-repeat group-hover:scale-105 transform" 
             style="background-image: url('{{ $user->img ? asset('assets/images/super-admin/' . $user->img) : '' }}'); background-size: cover; background-position: center;">
            @if(!$user->img)
                <div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-lg">
                    <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="flex-1">
        <label for="img" 
            class="block cursor-pointer bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/20 dark:to-purple-950/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800/30 hover:shadow-md transition">
            <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 mb-1">Upload new avatar</p>
            <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-1">JPG, PNG or GIF format</p>
            <p class="text-xs text-neutral-500 dark:text-neutral-500">Maximum size: 2MB • Recommended: 400x400px</p>
        </label>
    </div>
</div>

                            </div>

                            <form id="edit-profile-form" action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="file" id="img" name="img" accept=".png,.jpg,.jpeg,.gif" class="hidden">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <label for="name" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Full Name <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                               class="w-full px-4 py-3 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-neutral-700 dark:text-white transition-all duration-200 disabled:bg-neutral-100 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed" 
                                               placeholder="Enter your full name" required readonly disabled>
                                    </div>

                                    <div class="space-y-3">
                                        <label for="email" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Email Address <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                               class="w-full px-4 py-3 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-neutral-700 dark:text-white transition-all duration-200 disabled:bg-neutral-100 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed" 
                                               placeholder="Enter your email address" required readonly disabled>
                                    </div>

                                    <div class="space-y-3 md:col-span-1">
                                        <label for="role" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                            </svg>
                                            Role <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select id="role" name="role" 
                                                class="w-full px-4 py-3 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-neutral-700 dark:text-white transition-all duration-200 disabled:bg-neutral-100 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed" 
                                                required disabled>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="form-buttons" class="flex items-center justify-end space-x-4 mt-8 hidden">
                                    <button type="button" id="cancel-edit" 
                                            class="px-6 py-3 border-2 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-4 focus:ring-neutral-500/20 transition-all duration-200 font-medium">
                                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
                                        <span class="submit-text flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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

                            <div id="edit-button-container" class="flex justify-end mt-8">
                                <button type="button" id="enable-edit" 
                                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Profile
                                </button>
                            </div>
                        </div>

                        <div id="change-password" class="tab-content hidden">
                            <div class="max-w-lg mx-auto">
                                <div class="text-center mb-8">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/50 dark:to-purple-900/50 rounded-full mb-4 shadow-lg">
                                        <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200 mb-3">Change Password</h3>
                                    <p class="text-neutral-600 dark:text-neutral-400">Update your password to keep your account secure</p>
                                </div>

                                <form id="password-form" action="{{ route('users.updatePassword', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="space-y-6">
                                        <div class="space-y-3">
                                            <label for="password" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                New Password <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" id="password" name="password" 
                                                       class="w-full px-4 py-3 pr-12 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-neutral-700 dark:text-white transition-all duration-200 disabled:bg-neutral-100 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed" 
                                                       placeholder="Enter new password" required disabled minlength="6">
                                                <button type="button" 
                                                        class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-all duration-200" 
                                                        data-target="password" aria-label="Toggle password visibility">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Confirm Password <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                                       class="w-full px-4 py-3 pr-12 border-2 border-neutral-300 dark:border-neutral-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-neutral-700 dark:text-white transition-all duration-200 disabled:bg-neutral-100 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed" 
                                                       placeholder="Confirm new password" required disabled minlength="6">
                                                <button type="button" 
                                                        class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300 p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-all duration-200" 
                                                        data-target="password_confirmation" aria-label="Toggle password visibility">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="password-buttons" class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4 mt-8 hidden">
                                        <button type="button" id="cancel-password" 
                                                class="w-full sm:w-auto px-6 py-3 border-2 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-4 focus:ring-neutral-500/20 transition-all duration-200 font-medium">
                                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
                                            <span class="submit-text flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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

                                <div id="password-button-container" class="flex justify-center mt-8">
                                    <button type="button" id="enable-password-edit" 
                                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Change Password
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/iconify@3.1.1/iconify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/iconify/3.1.1/iconify.min.js"></script>

<script>
  window.Profile = {
    name: @json($user->name),
    email: @json($user->email),
    role: @json($user->role),
    img: @json($user->img ? asset('assets/images/super-admin/' . $user->img) : ''),
    initial: @json(strtoupper(substr($user->name, 0, 1)))
  };
</script>

<script src="{{ asset('assets/js/user.js') }}"></script>

<style>
.tab-button[aria-selected="true"] {
    color: #2563eb !important;
    border-bottom-color: #2563eb !important;
    background: linear-gradient(to bottom, rgba(59, 130, 246, 0.1), transparent) !important;
}

.dark .tab-button[aria-selected="true"] {
    color: #60a5fa !important;
    border-bottom-color: #60a5fa !important;
}

.notification-toast {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

@media (max-width: 640px) {
    .notification-toast {
        width: calc(100vw - 32px) !important;
        right: 16px !important;
        top: 16px !important;
    }
}

.border-b-3 {
    border-bottom-width: 3px;
}

input:disabled,
select:disabled {
    opacity: 0.7;
}

.dark input:disabled,
.dark select:disabled {
    opacity: 0.6;
}
</style>

@endsection