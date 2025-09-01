@extends('layout.layout')
@php
    $title = 'Midtrans API Key (Global)';
    $subTitle = 'Midtrans API Key';
    $script = '<script>
        // ======================== Upload Image Start =====================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                    $("#imagePreview").hide();
                    $("#imagePreview").fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        // ======================== Upload Image End =====================

        // ================== Password Show Hide Js Start ==========
        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on("click", function() {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
        // Call the function
        initializePasswordToggle(".toggle-password");
        // ========================= Password Show Hide Js End ===========================
    </script>';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-12">
            <div class="card h-full border-0">
                <div class="card-body p-6">

                    <div class="mb-5">
                        <h6 class=" font-semibold text-lg py-2">
                            Midtrans API Key Settings
                        </h6>
                    </div>

                    <div id="default-tab-content">
                        <div id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">

                            <form action="{{ route('admin.midtrans.store') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6">
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="merchant_id"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Merchant ID <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control rounded-lg" id="merchant_id"
                                                name="merchant_id"
                                                value="{{ old('merchant_id', $midtrans_settings->merchant_id ?? '') }}"
                                                placeholder="Enter Merchant ID" required>
                                            @error('merchant_id')
                                                <span class="text-danger-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="client_key"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Client
                                                Key
                                                <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control rounded-lg" id="client_key"
                                                name="client_key"
                                                value="{{ old('client_key', $midtrans_settings->client_key ?? '') }}"
                                                placeholder="Enter Client Key" required>
                                            @error('client_key')
                                                <span class="text-danger-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="server_key"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Server
                                                Key <span class="text-danger-600">*</span></label>
                                            <div class="relative">
                                                <input type="password" class="form-control rounded-lg pr-12" id="server_key"
                                                    name="server_key"
                                                    value="{{ old('server_key', $midtrans_settings->server_key ?? '') }}"
                                                    placeholder="Enter Server Key" required>
                                                <button type="button"
                                                    class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                                    data-toggle="#server_key">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                            @error('server_key')
                                                <span class="text-danger-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="environment"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Environment
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="form-control rounded-lg form-select" id="environment"
                                                name="environment" required>
                                                <option value="">Select Environment</option>
                                                <option value="sandbox"
                                                    {{ old('environment', $midtrans_settings->environment ?? '') == 'sandbox' ? 'selected' : '' }}>
                                                    Sandbox
                                                </option>
                                                <option value="production"
                                                    {{ old('environment', $midtrans_settings->environment ?? '') == 'production' ? 'selected' : '' }}>
                                                    Production
                                                </option>
                                            </select>
                                            @error('environment')
                                                <span class="text-danger-600 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-span-12">
                                        <div class="mb-5">
                                            <label for="webhook_url"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Webhook
                                                / Notification URL</label>
                                            <textarea name="webhook_url" class="form-control rounded-lg" id="webhook_url"
                                                placeholder="https://yourdomain.com/midtrans/notification" style="height: 100px;">{{ old('webhook_url', $midtrans_settings->webhook_url ?? '') }}</textarea>
                                            @error('webhook_url')
                                                <span class="text-danger-600 text-sm">{{ $message }}</span>
                                            @enderror
                                            <small class="text-gray-500 mt-1">
                                                URL ini akan menerima notifikasi dari Midtrans ketika status transaksi
                                                berubah
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-10 py-[10px] rounded-lg"
                                        onclick="window.history.back()">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="btn btn-primary border border-primary-600 text-base px-10 py-[10px] rounded-lg">
                                        Save Configuration
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-4">
            {{ session('error') }}
        </div>
    @endif
@endsection
