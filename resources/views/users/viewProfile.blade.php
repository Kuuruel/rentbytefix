@extends('layout.layout')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-4">
            <div class="user-grid-card relative border border-neutral-200 dark:border-neutral-600 rounded-2xl overflow-hidden bg-white dark:bg-neutral-700 h-full">
                <img src="{{ asset('assets/images/user-grid/user-grid-bg1.png') }}" alt="" class="w-full object-cover">
                <div class="pb-6 ms-6 mb-6 me-6 -mt-[100px]">
                    <div class="text-center border-b border-neutral-200 dark:border-neutral-600">
                        @if($user->img)
                            <img src="{{ asset('assets/images/super-admin/' . $user->img) }}" alt="" class="border border-white border-2 w-[200px] h-[200px] rounded-full object-cover mx-auto">
                        @else
                            <img src="{{ asset('assets/images/user-grid/user-grid-img14.png') }}" alt="" class="border border-white border-2 w-[200px] h-[200px] rounded-full object-cover mx-auto">
                        @endif
                        <h6 class="mb-0 mt-4">{{ $user->name }}</h6>
                        <span class="text-secondary-light mb-4">{{ $user->email }}</span>
                    </div>
                    <div class="mt-6">
                        <h6 class="text-xl mb-4">Personal Info</h6>
                        <ul>
                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">Full Name</span>
                                <span class="w-[70%] text-secondary-light font-medium">: {{ $user->name }}</span>
                            </li>
                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">Email</span>
                                <span class="w-[70%] text-secondary-light font-medium">: {{ $user->email }}</span>
                            </li>
                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">Role</span>
                                <span class="w-[70%] text-secondary-light font-medium">: {{ ucfirst($user->role) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8">
            <div class="card h-full border-0">
                <div class="card-body p-6">

                    <ul class="tab-style-gradient flex flex-wrap text-sm font-medium text-center mb-5" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                        <li role="presentation">
                            <button class="py-2.5 px-4 border-t-2 font-semibold text-base inline-flex items-center gap-3 text-neutral-600" id="edit-profile-tab" data-tabs-target="#edit-profile" type="button" role="tab" aria-controls="edit-profile" aria-selected="true">
                                Edit Profile
                            </button>
                        </li>
                        <li role="presentation">
                            <button class="py-2.5 px-4 border-t-2 font-semibold text-base inline-flex items-center gap-3 text-neutral-600 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="change-password-tab" data-tabs-target="#change-password" type="button" role="tab" aria-controls="change-password" aria-selected="false">
                                Change Password
                            </button>
                        </li>
                    </ul>

                    <div id="default-tab-content">
                        <div id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                            <h6 class="text-base text-neutral-600 dark:text-neutral-200 mb-4">Profile Image</h6>
                            <div class="mb-6 mt-4">
                                <div class="avatar-upload relative inline-block">
                                    <div class="avatar-edit absolute bottom-0 right-0 z-10 cursor-pointer">
                                        <!-- Hanya 1 input file: id & name = img -->
                                        <input type='file' id="img" name="img" accept=".png, .jpg, .jpeg" class="hidden">
                                        <label for="img" class="w-8 h-8 flex justify-center items-center bg-primary-100 dark:bg-primary-600/25 text-primary-600 dark:text-primary-400 border border-primary-600 hover:bg-primary-100 text-lg rounded-full cursor-pointer">
                                            <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                        </label>
                                    </div>
                                    <div class="avatar-preview">
                                        <div id="imagePreview" class="w-[140px] h-[140px] rounded-full border-4 border-white shadow-md bg-center bg-cover mx-auto"
                                             style="background-image: url('{{ $user->img ? asset('assets/images/super-admin/' . $user->img) : asset('assets/images/user-grid/user-grid-img14.png') }}');">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6">
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="name" class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Full Name <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control rounded-lg w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="name" name="name" value="{{ $user->name }}" placeholder="Enter Full Name" required>
                                        </div>
                                    </div>

                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="email" class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Email <span class="text-danger-600">*</span></label>
                                            <input type="email" class="form-control rounded-lg w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="email" name="email" value="{{ $user->email }}" placeholder="Enter email address" required>
                                        </div>
                                    </div>

                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="role" class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Role <span class="text-danger-600">*</span></label>
                                            <select class="form-control rounded-lg form-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="role" name="role" required>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- HIDDEN input img sudah ada di atas, jadi jangan duplikat di sini -->
                                </div>

                                <div class="flex items-center justify-center gap-3 mt-4">
                                    <a href="javascript:history.back()" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-14 py-3 rounded-lg hover:bg-red-50">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 bg-primary-600 text-white text-base px-14 py-3 rounded-lg hover:bg-primary-700">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="change-password" role="tabpanel" aria-labelledby="change-password-tab" class="hidden mt-6">
                            <form action="{{ route('users.updatePassword', $user->id) }}" method="POST">
                                @csrf
                                <div class="mb-5">
                                    <label for="your-password" class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">New Password <span class="text-danger-600">*</span></label>
                                    <div class="relative">
                                        <input type="password" class="form-control rounded-lg w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="your-password" name="password" placeholder="Enter New Password*" required>
                                        <span class="toggle-password ri-eye-line cursor-pointer absolute right-0 top-1/2 -translate-y-1/2 mr-4 text-secondary-light" data-toggle="#your-password"></span>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label for="confirm-password" class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Confirmed Password <span class="text-danger-600">*</span></label>
                                    <div class="relative">
                                        <input type="password" class="form-control rounded-lg w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="confirm-password" name="password_confirmation" placeholder="Confirm Password*" required>
                                        <span class="toggle-password ri-eye-line cursor-pointer absolute right-0 top-1/2 -translate-y-1/2 mr-4 text-secondary-light" data-toggle="#confirm-password"></span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-center gap-3">
                                    <a href="javascript:history.back()" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-14 py-3 rounded-lg hover:bg-red-50">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 bg-primary-600 text-white text-base px-14 py-3 rounded-lg hover:bg-primary-700">
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT: letakkan langsung di blade (hindari @php string yang crash) -->
    <script>
        (function () {
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('imagePreview').style.backgroundImage = 'url(' + e.target.result + ')';
                        // simple fade effect using opacity (jQuery not required)
                        var el = document.getElementById('imagePreview');
                        el.style.opacity = 0;
                        setTimeout(function(){ el.style.opacity = 1; }, 50);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            var imgInput = document.getElementById('img');
            if (imgInput) {
                imgInput.addEventListener('change', function() {
                    readURL(this);
                });
            }

            function initializePasswordToggle(selector) {
                var toggles = document.querySelectorAll(selector);
                toggles.forEach(function(t){
                    t.addEventListener('click', function(){
                        this.classList.toggle('ri-eye-off-line');
                        var input = document.querySelector(this.getAttribute('data-toggle'));
                        if (!input) return;
                        if (input.getAttribute('type') === 'password') {
                            input.setAttribute('type', 'text');
                        } else {
                            input.setAttribute('type', 'password');
                        }
                    });
                });
            }
            initializePasswordToggle('.toggle-password');
        })();
    </script>

@endsection
