@extends('layout.layout')
@php
    $title = 'Payment Error';
    $subTitle = 'Pembayaran Gagal';
@endphp

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <iconify-icon icon="ph:x-circle" class="text-red-600 dark:text-red-400 text-2xl"></iconify-icon>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Pembayaran Gagal</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Maaf, terjadi kesalahan saat memproses pembayaran Anda.
            </p>
            
            @if(isset($order_id))
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">Order ID</div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $order_id }}</div>
            </div>
            @endif
            
            <div class="space-y-3">
                <a href="{{ route('landlord.index') }}" 
                   class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors block text-center">
                    Coba Lagi
                </a>
                <a href="{{ route('landlord.index') }}" 
                   class="w-full bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors block text-center">
                    Kembali ke Properties
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
