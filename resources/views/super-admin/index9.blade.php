@extends('layout.layout')
@php
    $title = 'Midtrans API Key (Global)';
    $subTitle = 'Midtrans API Key';
    $hasData = isset($midtrans_settings) && $midtrans_settings;
    $isEditMode = session('edit_mode', false) || !$hasData;
    $script = '<script>
        $(document).ready(function() {
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

            // Initialize password toggle
            initializePasswordToggle(".toggle-password");

            // Auto hide alerts
            setTimeout(function() {
                $(".alert").fadeOut();
            }, 5000);

            // Form validation
            $("#midtransForm").on("submit", function() {
                $("#submitBtn").prop("disabled", true).html(
                    "<i class=\"ri-loader-2-line me-1 animate-spin\"></i> Menyimpan...");
            });

            // Toggle edit mode
            $("#editBtn").on("click", function() {
                $(".form-control, .form-select").prop("disabled", false);
                $(this).addClass("d-none");
                $("#cancelBtn, #submitBtn").removeClass("d-none");
                $("#viewMode").addClass("d-none");
                $("#editMode").removeClass("d-none");
            });

            $("#cancelBtn").on("click", function() {
                location.reload();
            });
        });
    </script>';
@endphp

@section('content')
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    @if (session('info'))
        <div id="info-toast"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    ">
            <div
                style="
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
                <div style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div
                            style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                            <iconify-icon icon="ph:info-fill" style="font-size: 18px; color: white;"></iconify-icon>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h4
                                style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">
                                Info!</h4>
                            <p
                                style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">
                                {{ session('info') }}</p>
                        </div>
                        <button onclick="this.closest('#info-toast').remove()"
                            style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    "
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
                            <iconify-icon icon="ph:x" style="font-size: 14px;"></iconify-icon>
                        </button>
                    </div>
                </div>
                <div
                    style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                    <div class="notification-progress"
                        style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 4s linear;
                ">
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('info-toast');
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';

                const progressBar = toast.querySelector('.notification-progress');
                setTimeout(() => progressBar.style.width = '0%', 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 4500);
            }, 10);
        </script>
    @endif

    @if (session('delete'))
        <div id="delete-toast"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    ">
            <div
                style="
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
                <div style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div
                            style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                            <iconify-icon icon="ph:trash" style="font-size: 18px; color: white;"></iconify-icon>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h4
                                style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">
                                Deleted!</h4>
                            <p
                                style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">
                                {{ session('delete') }}</p>
                        </div>
                        <button onclick="this.closest('#delete-toast').remove()"
                            style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    "
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
                            ×
                        </button>
                    </div>
                </div>
                <div
                    style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                    <div class="notification-progress"
                        style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 4s linear;
                ">
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('delete-toast');
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';

                const progressBar = toast.querySelector('.notification-progress');
                setTimeout(() => progressBar.style.width = '0%', 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 4500);
            }, 10);
        </script>
    @endif{{-- Alert Messages --}}
    @if (session('success'))
        <div id="success-toast"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    ">
            <div
                style="
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
                <div style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div
                            style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                            <iconify-icon icon="ph:check-circle-fill" style="font-size: 18px; color: white;"></iconify-icon>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h4
                                style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">
                                Success!</h4>
                            <p
                                style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">
                                {{ session('success') }}</p>
                        </div>
                        <button onclick="this.closest('#success-toast').remove()"
                            style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    "
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
                            ×
                        </button>
                    </div>
                </div>
                <div
                    style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                    <div class="notification-progress"
                        style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 4s linear;
                ">
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('success-toast');
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';

                const progressBar = toast.querySelector('.notification-progress');
                setTimeout(() => progressBar.style.width = '0%', 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 4500);
            }, 10);
        </script>
    @endif

    @if (session('error'))
        <div id="error-toast"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    ">
            <div
                style="
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
                <div style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div
                            style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                            ❌
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h4
                                style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">
                                Error!</h4>
                            <p
                                style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">
                                {{ session('error') }}</p>
                        </div>
                        <button onclick="this.closest('#error-toast').remove()"
                            style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    "
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
                            ×
                        </button>
                    </div>
                </div>
                <div
                    style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                    <div class="notification-progress"
                        style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 6s linear;
                ">
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('error-toast');
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';

                const progressBar = toast.querySelector('.notification-progress');
                setTimeout(() => progressBar.style.width = '0%', 100);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 6000);
            }, 10);
        </script>
    @endif



    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-12">
            <div class="card h-full border-0">
                <div class="card-body p-6">

                    <div class="mb-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <h6 class="font-semibold text-lg py-2">
                                    Midtrans API Key Settings
                                </h6>
                                <p class="text-sm text-gray-600 mt-1">
                                    Midtrans credential configuration for payment processing
                                </p>
                            </div>

                            {{-- Status Badge --}}
                            {{-- @if ($hasData)
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="ri-check-circle-line mr-1"></i>
                                        Configured
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $midtrans_settings->environment === 'production' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($midtrans_settings->environment) }}
                                    </span>
                                </div>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="ri-settings-line mr-1"></i>
                                    Not Configured
                                </span>
                            @endif --}}
                        </div>
                    </div>

                    <div id="default-tab-content">
                        <div id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">

                            <form action="{{ route('admin.midtrans.store') }}" method="POST" id="midtransForm">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6">
                                    {{-- Merchant ID --}}
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="merchant_id"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Merchant ID <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control rounded-lg @error('merchant_id') border-danger-600 @enderror"
                                                id="merchant_id" name="merchant_id"
                                                value="{{ old('merchant_id', $midtrans_settings->merchant_id ?? '') }}"
                                                placeholder="Masukkan Merchant ID" maxlength="255"
                                                {{ $hasData && !$isEditMode ? 'disabled' : 'required' }}>
                                            @error('merchant_id')
                                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Client Key --}}
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="client_key"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Client Key <span class="text-danger-600">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control rounded-lg @error('client_key') border-danger-600 @enderror"
                                                id="client_key" name="client_key"
                                                value="{{ old('client_key', $midtrans_settings->client_key ?? '') }}"
                                                placeholder="Masukkan Client Key" maxlength="255"
                                                {{ $hasData && !$isEditMode ? 'disabled' : 'required' }}>
                                            @error('client_key')
                                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Server Key --}}
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="server_key"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Server Key <span class="text-danger-600">*</span>
                                            </label>
                                            <div class="relative w-full">
                                                <input type="password" id="server_key" name="server_key"
                                                    class="form-control w-full rounded-lg pr-12 @error('server_key') border-danger-600 @enderror"
                                                    value="{{ old('server_key', $midtrans_settings->server_key ?? '') }}"
                                                    placeholder="Masukkan Server Key" maxlength="255"
                                                    {{ $hasData && !$isEditMode ? 'disabled' : 'required' }}>

                                                <!-- Eye toggle button -->
                                                <button type="button"
                                                    class="toggle-password absolute right-0 top-0 h-full flex items-center px-3 text-gray-500 hover:text-gray-700"
                                                    data-toggle="#server_key">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>

                                            @error('server_key')
                                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Environment --}}
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="environment"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Environment <span class="text-danger-600">*</span>
                                            </label>
                                            <select
                                                class="form-control bg-rounded-lg form-select @error('environment') border-danger-600 @enderror"
                                                id="environment" name="environment"
                                                {{ $hasData && !$isEditMode ? 'disabled' : 'required' }}>
                                                <option value="">Select Environment</option>
                                                <option  value="sandbox"
                                                    {{ old('environment', $midtrans_settings->environment ?? '') == 'sandbox' ? 'selected' : '' }}>
                                                    Sandbox (Testing)
                                                </option>
                                                <option value="production"
                                                    {{ old('environment', $midtrans_settings->environment ?? '') == 'production' ? 'selected' : '' }}>
                                                    Production (Live)
                                                </option>
                                            </select>
                                            @error('environment')
                                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Webhook URL --}}

                                    <div class="col-span-12">
                                        <div class="mb-5">
                                            <label for="webhook_url"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Webhook / Notification URL
                                                {{-- <span class="text-gray-400 text-xs">(Opsional)</span> --}}
                                            </label>
                                            <textarea name="webhook_url"
                                                class="form-control rounded-lg text-primary-600 @error('webhook_url') border-danger-600 @enderror"
                                                id="webhook_url" placeholder="https://yourdomain.com/midtrans/notification"
                                                style="height: 100px; color: #2563eb;" maxlength="500" {{ $hasData && !$isEditMode ? 'disabled' : '' }}>{{ old('webhook_url', $midtrans_settings->webhook_url ?? '') }}</textarea>
                                            @error('webhook_url')
                                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                            <small class="text-gray-500 mt-1 block">
                                                <i class="ri-information-line me-1"></i>
                                                This URL will receive notifications from Midtrans when the transaction
                                                status changes.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="flex items-center justify-end gap-3 mt-6">
                                    @if ($hasData && !$isEditMode)
                                        {{-- View Mode Buttons --}}
                                        <div id="viewMode">
                                            {{-- <button type="button"
                                                class="border border-gray-300 bg-white text-gray-700 text-base px-6 py-[10px] rounded-lg hover:bg-gray-50"
                                                onclick="window.history.back()">
                                                <i class="ri-arrow-left-line me-1"></i>
                                                Back
                                            </button> --}}
                                            <button type="button" id="editBtn"
                                                class="btn btn-primary border border-primary-600 text-base px-6 py-[10px] rounded-lg">
                                                <i class="ri-edit-line me-1"></i>
                                                Edit Configuration
                                            </button>
                                        </div>

                                        {{-- Edit Mode Buttons (Hidden by default) --}}
                                        <div id="editMode" class="d-none">
                                            {{-- <button type="button" id="cancelBtn"
                                                class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-10 py-[10px] rounded-lg hover:bg-danger-50">
                                                Cancel
                                            </button> --}}
                                            <button type="submit" id="submitBtn"
                                                class="btn btn-primary border border-primary-600 text-base px-10 py-[10px] rounded-lg">
                                                Update Configuration
                                            </button>
                                        </div>
                                    @else
                                        {{-- New/Edit Mode Buttons --}}
                                        <button type="button"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-10 py-[10px] rounded-lg hover:bg-danger-50"
                                            onclick="window.history.back()">
                                            Cancel
                                        </button>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary border border-primary-600 text-base px-10 py-[10px] rounded-lg">
                                            {{ $hasData ? 'Update Configuration' : 'Save Configuration' }}
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
