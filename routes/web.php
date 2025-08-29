<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiapplicationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ComponentspageController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('super-admin.index');
        } else {
            return redirect()->route('landlord.index');
        }
    }
    return redirect()->route('showSigninForm');
});

Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/forgot-password', 'forgotPassword')->name('forgotPassword');
        Route::get('/sign-in', 'showSigninForm')->name('showSigninForm');
        Route::get('/sign-up', 'signup')->name('signup');
        Route::post('/sign-in', 'signin')->name('signin');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('super-admin')->group(function () {
        Route::controller(SuperAdminController::class)->group(function () {
            Route::get('/', 'index')->name('super-admin.index');
            Route::get('/index2', 'index2')->name('super-admin.index2');
            Route::get('/index3', 'index3')->name('super-admin.index3');
            Route::get('/index4', 'index4')->name('super-admin.index4');
            Route::get('/index5', 'index5')->name('super-admin.index5');
            Route::get('/index6', 'index6')->name('super-admin.index6');
            Route::get('/index7', 'index7')->name('super-admin.index7');
            Route::get('/index8', 'index8')->name('super-admin.index8');
            Route::get('/index9', 'index9')->name('super-admin.index9');
        });
    });

    Route::prefix('landlord')->group(function () {
        Route::controller(LandlordController::class)->group(function () {
            Route::get('/', 'index')->name('landlord.index');
            Route::get('/index2', 'index2')->name('landlord.index2');
            Route::get('/index3', 'index3')->name('landlord.index3');
            Route::get('/index4', 'index4')->name('landlord.index4');
            Route::get('/index5', 'index5')->name('landlord.index5');
            Route::get('/index6', 'index6')->name('landlord.index6');
            Route::get('/index7', 'index7')->name('landlord.index7');
            Route::get('/index8', 'index8')->name('landlord.index8');
            Route::get('/index9', 'index9')->name('landlord.index9');
        });
    });

    Route::controller(HomeController::class)->group(function () {
        Route::get('calendar-Main', 'calendarMain')->name('calendarMain');
        Route::get('chatempty', 'chatempty')->name('chatempty');
        Route::get('chat-message', 'chatMessage')->name('chatMessage');
        Route::get('chat-profile', 'chatProfile')->name('chatProfile');
        Route::get('email', 'email')->name('email');
        Route::get('faq', 'faq')->name('faq');
        Route::get('gallery', 'gallery')->name('gallery');
        Route::get('image-upload', 'imageUpload')->name('imageUpload');
        Route::get('kanban', 'kanban')->name('kanban');
        Route::get('page-error', 'pageError')->name('pageError');
        Route::get('pricing', 'pricing')->name('pricing');
        Route::get('starred', 'starred')->name('starred');
        Route::get('terms-condition', 'termsCondition')->name('termsCondition');
        Route::get('veiw-details', 'veiwDetails')->name('veiwDetails');
        Route::get('widgets', 'widgets')->name('widgets');
    });

    Route::prefix('aiapplication')->group(function () {
        Route::controller(AiapplicationController::class)->group(function () {
            Route::get('/code-generator', 'codeGenerator')->name('codeGenerator');
            Route::get('/code-generatornew', 'codeGeneratorNew')->name('codeGeneratorNew');
            Route::get('/image-generator', 'imageGenerator')->name('imageGenerator');
            Route::get('/text-generator', 'textGenerator')->name('textGenerator');
            Route::get('/text-generatornew', 'textGeneratorNew')->name('textGeneratorNew');
            Route::get('/video-generator', 'videoGenerator')->name('videoGenerator');
            Route::get('/voice-generator', 'voiceGenerator')->name('voiceGenerator');
        });
    });

    Route::prefix('chart')->group(function () {
        Route::controller(ChartController::class)->group(function () {
            Route::get('/column-chart', 'columnChart')->name('columnChart');
            Route::get('/line-chart', 'lineChart')->name('lineChart');
            Route::get('/pie-chart', 'pieChart')->name('pieChart');
        });
    });

    Route::prefix('componentspage')->group(function () {
        Route::controller(ComponentspageController::class)->group(function () {
            Route::get('/alert', 'alert')->name('alert');
            Route::get('/avatar', 'avatar')->name('avatar');
            Route::get('/badges', 'badges')->name('badges');
            Route::get('/button', 'button')->name('button');
            Route::get('/calendar', 'calendar')->name('calendar');
            Route::get('/card', 'card')->name('card');
            Route::get('/carousel', 'carousel')->name('carousel');
            Route::get('/colors', 'colors')->name('colors');
            Route::get('/dropdown', 'dropdown')->name('dropdown');
            Route::get('/imageupload', 'imageUpload')->name('imageUpload');
            Route::get('/list', 'list')->name('list');
            Route::get('/pagination', 'pagination')->name('pagination');
            Route::get('/progress', 'progress')->name('progress');
            Route::get('/radio', 'radio')->name('radio');
            Route::get('/star-rating', 'starRating')->name('starRating');
            Route::get('/switch', 'switch')->name('switch');
            Route::get('/tabs', 'tabs')->name('tabs');
            Route::get('/tags', 'tags')->name('tags');
            Route::get('/tooltip', 'tooltip')->name('tooltip');
            Route::get('/typography', 'typography')->name('typography');
            Route::get('/videos', 'videos')->name('videos');
        });
    });

    Route::prefix('cryptocurrency')->group(function () {
        Route::controller(CryptocurrencyController::class)->group(function () {
            Route::get('/wallet', 'wallet')->name('wallet');
        });
    });

    Route::prefix('forms')->group(function () {
        Route::controller(FormsController::class)->group(function () {
            Route::get('/form', 'form')->name('form');
            Route::get('/form-layout', 'formLayout')->name('formLayout');
            Route::get('/form-validation', 'formValidation')->name('formValidation');
            Route::get('/wizard', 'wizard')->name('wizard');
        });
    });

    Route::prefix('invoice')->group(function () {
        Route::controller(InvoiceController::class)->group(function () {
            Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
            Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
            Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
            Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
        });
    });

    Route::prefix('settings')->group(function () {
        Route::controller(SettingsController::class)->group(function () {
            Route::get('/company', 'company')->name('company');
            Route::get('/currencies', 'currencies')->name('currencies');
            Route::get('/language', 'language')->name('language');
            Route::get('/notification', 'notification')->name('notification');
            Route::get('/notification-alert', 'notificationAlert')->name('notificationAlert');
            Route::get('/payment-gateway', 'paymentGateway')->name('paymentGateway');
            Route::get('/theme', 'theme')->name('theme');
        });
    });

    Route::prefix('table')->group(function () {
        Route::controller(TableController::class)->group(function () {
            Route::get('/table-basic', 'tableBasic')->name('tableBasic');
            Route::get('/table-data', 'tableData')->name('tableData');
        });
    });

    Route::prefix('users')->group(function () {
        Route::controller(UsersController::class)->group(function () {
            Route::get('/add-user', 'addUser')->name('addUser');
            Route::post('/add-user', 'store')->name('users.store');
            Route::get('/users-grid', 'usersGrid')->name('usersGrid');
            Route::get('/users-list', 'usersList')->name('usersList');
            Route::get('/view-profile/{id?}', 'viewProfile')->name('viewProfile');
            Route::put('/update-profile/{id}', 'update')->name('users.update');
            Route::post('/update-password/{id}', 'updatePassword')->name('users.updatePassword');
            Route::delete('/delete/{id}', 'destroy')->name('users.destroy');
        });
    });

    Route::resource('tenants', TenantController::class);

    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::post('/', [TenantController::class, 'store'])->name('store');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
        Route::put('/{tenant}', [TenantController::class, 'update'])->name('update');
        Route::delete('/{tenant}', [TenantController::class, 'destroy'])->name('destroy');
    });
});