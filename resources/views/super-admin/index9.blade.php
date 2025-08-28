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

                            <form action="#">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6">
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="name"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Merchant ID <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control rounded-lg" id="name"
                                                placeholder="Enter Merchant ID">
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="email"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Client
                                                Key
                                                <span class="text-danger-600">*</span></label>
                                            <input type="email" class="form-control rounded-lg" id="email"
                                                placeholder="Enter Client Key">
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="number"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Server
                                                Key</label>
                                            <input type="email" class="form-control rounded-lg" id="number"
                                                placeholder="Enter Server Key">
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="depart" yang do
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Environment
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="form-control rounded-lg form-select" id="depart">
                                                <option>Sandbox </option>
                                                <option>Production </option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-span-12">
                                        <div class="mb-5">
                                            <label for="desc"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Webhook
                                                / Notification URL</label>
                                            <textarea name="#0" class="form-control rounded-lg" id="desc" placeholder="url here..."
                                                style="height: 100px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-10 py-[10px] rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="button"
                                        class="btn btn-primary border border-primary-600 text-base px-10 py-[10px] rounded-lg">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
