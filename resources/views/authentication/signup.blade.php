<!DOCTYPE html>
<html lang="en">

<head>
    <x-head/>
</head>

<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">

    <section class="bg-white dark:bg-dark-2 flex flex-wrap min-h-[100vh]">
        <div class="lg:w-1/2 lg:block hidden">
            <div class="flex items-center flex-col h-full justify-center">
                <img src="{{ asset('assets/images/r-logos.png') }}" alt="">
            </div>
        </div>
        <div class="lg:w-1/2 py-8 px-6 flex flex-col justify-center">
            <div class="lg:max-w-[464px] mx-auto w-full">
                <div>
                    <h4 class="mb-3">Create Tenant Account</h4>
                    <p class="mb-8 text-secondary-light text-lg">Welcome! Please enter your details to create a tenant account</p>
                </div>
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="f7:person"></iconify-icon>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="Full Name" required>
                    </div>
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="Email" required>
                    </div>
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="material-symbols:location-on"></iconify-icon>
                        </span>

                        @php
                            $selectedCountry = old('country', $user->country ?? '');
                        @endphp

                        <select id="formCountry" name="country" required
                            class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl w-full text-neutral-900 dark:text-white">
                            <option value="" {{ $selectedCountry === '' ? 'selected' : '' }}>Choose City</option>
                            
                            <option value="Jakarta" @selected($selectedCountry === 'Jakarta')>Jakarta</option>
                            <option value="Surabaya" @selected($selectedCountry === 'Surabaya')>Surabaya</option>
                            <option value="Bandung" @selected($selectedCountry === 'Bandung')>Bandung</option>
                            <option value="Medan" @selected($selectedCountry === 'Medan')>Medan</option>
                            <option value="Semarang" @selected($selectedCountry === 'Semarang')>Semarang</option>
                            <option value="Makassar" @selected($selectedCountry === 'Makassar')>Makassar</option>
                            <option value="Palembang" @selected($selectedCountry === 'Palembang')>Palembang</option>
                            <option value="Denpasar" @selected($selectedCountry === 'Denpasar')>Denpasar</option>
                            <option value="Yogyakarta" @selected($selectedCountry === 'Yogyakarta')>Yogyakarta</option>
                            <option value="Malang" @selected($selectedCountry === 'Malang')>Malang</option>
                            <option value="Batam" @selected($selectedCountry === 'Batam')>Batam</option>
                            <option value="Pekanbaru" @selected($selectedCountry === 'Pekanbaru')>Pekanbaru</option>
                            <option value="Balikpapan" @selected($selectedCountry === 'Balikpapan')>Balikpapan</option>
                            <option value="Banjarmasin" @selected($selectedCountry === 'Banjarmasin')>Banjarmasin</option>
                            <option value="Pontianak" @selected($selectedCountry === 'Pontianak')>Pontianak</option>
                            <option value="Manado" @selected($selectedCountry === 'Manado')>Manado</option>
                            <option value="Padang" @selected($selectedCountry === 'Padang')>Padang</option>
                            <option value="Samarinda" @selected($selectedCountry === 'Samarinda')>Samarinda</option>
                            <option value="Bandar Lampung" @selected($selectedCountry === 'Bandar Lampung')>Bandar Lampung</option>
                            <option value="Bogor" @selected($selectedCountry === 'Bogor')>Bogor</option>
                            <option value="Bekasi" @selected($selectedCountry === 'Bekasi')>Bekasi</option>
                            <option value="Depok" @selected($selectedCountry === 'Depok')>Depok</option>
                            <option value="Tangerang" @selected($selectedCountry === 'Tangerang')>Tangerang</option>
                            <option value="Solo" @selected($selectedCountry === 'Solo')>Solo (Surakarta)</option>
                            <option value="Cirebon" @selected($selectedCountry === 'Cirebon')>Cirebon</option>
                            <option value="Kupang" @selected($selectedCountry === 'Kupang')>Kupang</option>
                            <option value="Jayapura" @selected($selectedCountry === 'Jayapura')>Jayapura</option>
                            <option value="Ambon" @selected($selectedCountry === 'Ambon')>Ambon</option>
                            <option value="Mataram" @selected($selectedCountry === 'Mataram')>Mataram</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="relative">
                            <div class="icon-field">
                                <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input type="password" name="password" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" id="password" placeholder="Password" required>
                            </div>
                            <span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#password"></span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="relative">
                            <div class="icon-field">
                                <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" id="password_confirmation" placeholder="Confirm Password" required>
                            </div>
                            <span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#password_confirmation"></span>
                        </div>
                        <span class="mt-3 text-sm text-secondary-light">Your password must have at least 6 characters</span>
                    </div>
                    <div class="mt-6">
                        <div class="flex justify-between gap-2">
                            <div class="form-check style-check flex items-start gap-2">
                                <input class="form-check-input border border-neutral-300 mt-1.5" type="checkbox" value="" id="condition" required>
                                <label class="text-sm" for="condition">
                                    By creating an account means you agree to the
                                    <a href="javascript:void(0)" class="text-primary-600 font-semibold">Terms & Conditions</a> and our
                                    <a href="javascript:void(0)" class="text-primary-600 font-semibold">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8">Create Tenant Account</button>

                    <div class="mt-8 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="{{ route('showSigninForm') }}" class="text-primary-600 font-semibold hover:underline">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function initializePasswordToggle(toggleSelector) {
            const toggles = document.querySelectorAll(toggleSelector);
            toggles.forEach(function(toggle) {
                toggle.addEventListener("click", function() {
                    this.classList.toggle("ri-eye-off-line");
                    const input = document.querySelector(this.getAttribute("data-toggle"));
                    if (input) {
                        input.type = input.type === "password" ? "text" : "password";
                    }
                });
            });
        }
        initializePasswordToggle(".toggle-password");
    </script>

    <x-script />

</body>
</html>