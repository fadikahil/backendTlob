<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeatureSectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ReportReasonController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SeoSettingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\TipController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\WebhookController;
use App\Models\UserVerification;
use App\Services\CachingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', static function () {
    if (Auth::user()) {
        return redirect('/home');
    }
    return view('auth.login');
});
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetPassword'])->name('password.reset');;
Route::get('reset-password-successful', [ForgotPasswordController::class, 'resetPasswordSuccessful'])->name('reset-password-successful');

Route::get('page/privacy-policy', static function () {
    $privacy_policy = CachingService::getSystemSettings('privacy_policy');
    echo htmlspecialchars_decode($privacy_policy);
})->name('public.privacy-policy');

Route::get('page/contact-us', static function () {
    $contact_us = CachingService::getSystemSettings('contact_us');
    echo htmlspecialchars_decode($contact_us);
})->name('public.contact-us');


Route::get('page/terms-conditions', static function () {
    $terms_conditions = CachingService::getSystemSettings('terms_conditions');
    echo htmlspecialchars_decode($terms_conditions);
})->name('public.terms-conditions');


Route::group(['prefix' => 'webhook'], static function () {
    Route::post('/stripe', [WebhookController::class, 'stripe']);
    Route::post('/paystack', [WebhookController::class, 'paystack']);
    Route::post('/razorpay', [WebhookController::class, 'razorpay']);
    Route::post('/phonePe', [WebhookController::class, 'phonePe']);
});
Route::get('response/paystack/success', [WebhookController::class, 'paystackSuccessCallback'])->name('paystack.success');
Route::get('response/phonepe/success', [WebhookController::class, 'phonePeSuccessCallback'])->name('phonepe.success');
Route::get('response/paystack/success/web', [SettingController::class, 'paystackPaymentSucesss'])->name('paystack.success.web');
Route::get('response/phonepe/success/web', [SettingController::class, 'phonepePaymentSucesss'])->name('phonepe.success.web');
/* Non-Authenticated Common Functions */
Route::group(['prefix' => 'common'], static function () {
    Route::get('/js/lang', [Controller::class, 'readLanguageFile'])->name('common.language.read');
});
Route::group(['prefix' => 'install'], static function () {
    Route::get('purchase-code', [InstallerController::class, 'purchaseCodeIndex'])->name('install.purchase-code.index');
    Route::post('purchase-code', [InstallerController::class, 'checkPurchaseCode'])->name('install.purchase-code.post');
    Route::get('php-function', [InstallerController::class, 'phpFunctionIndex'])->name('install.php-function.index');
});

Route::group(['middleware' => ['auth', 'language']], static function () {
    /*** Authenticated Common Functions ***/
    Route::group(['prefix' => 'common'], static function () {
        Route::put('/change-row-order', [Controller::class, 'changeRowOrder'])->name('common.row-order.change');
        Route::put('/change-status', [Controller::class, 'changeStatus'])->name('common.status.change');
    });


    /*** Home Module : START ***/
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('change-password', [HomeController::class, 'changePasswordIndex'])->name('change-password.index');
    Route::post('change-password', [HomeController::class, 'changePasswordUpdate'])->name('change-password.update');

    Route::get('change-profile', [HomeController::class, 'changeProfileIndex'])->name('change-profile.index');
    Route::post('change-profile', [HomeController::class, 'changeProfileUpdate'])->name('change-profile.update');
    /*** Home Module : END ***/

    /*** Category Module : START ***/
    // Test route for debugging
    Route::get('test-parent-categories', function() {
        $controller = new \App\Http\Controllers\CategoryController();
        $request = new \Illuminate\Http\Request(['type' => 'service_experience']);
        return $controller->getParentCategories($request);
    });

    // Test view for debugging
    Route::get('test-parent-categories-view', function() {
        return view('test-parent-categories');
    });

    // Make this route accessible without authentication for testing
    Route::get('category/get-parent-categories', [CategoryController::class, 'getParentCategories'])->name('category.get-parent-categories');

    Route::middleware(['auth'])->group(function () {
        // Route resource must be after specific routes to avoid conflicts
        Route::group(['prefix' => 'category'], static function () {
            Route::get('/service-experience', [CategoryController::class, 'serviceExperienceCategories'])->name('category.service.experience');
            Route::get('/providers', [CategoryController::class, 'providerCategories'])->name('category.providers');
            Route::get('/create-service', [CategoryController::class, 'createServiceCategory'])->name('category.create.service');
            Route::get('/create-provider', [CategoryController::class, 'createProviderCategory'])->name('category.create.provider');
            Route::get('/{id}/subcategories', [CategoryController::class, 'getSubCategories'])->name('category.subcategories');
            Route::get('/{id}/custom-fields', [CategoryController::class, 'customFields'])->name('category.custom-fields');
            Route::get('/{id}/custom-fields/show', [CategoryController::class, 'getCategoryCustomFields'])->name('category.custom-fields.show');
            Route::delete('/{id}/custom-fields/{customFieldID}/delete', [CategoryController::class, 'destroyCategoryCustomField'])->name('category.custom-fields.destroy');
            Route::get('/categories/order', [CategoryController::class, 'categoriesReOrder'])->name('category.order');
            Route::post('categories/change-order', [CategoryController::class, 'updateOrder'])->name('category.order.change');
            Route::get('/{id}/sub-category/change-order', [CategoryController::class, 'subCategoriesReOrder'])->name('sub.category.order.change');
            // Add a specific route for showing categories
            Route::get('/show/{category}', [CategoryController::class, 'show'])->name('category.show');
        });
        
        Route::resource('category', CategoryController::class)->except(['show']);
    });
    /*** Category Module : END ***/

    /*** Custom Field Module : START ***/
    Route::group(['prefix' => 'custom-fields'], static function () {
        Route::post('/{id}/value/add', [CustomFieldController::class, 'addCustomFieldValue'])->name('custom-fields.value.add');
        Route::get('/{id}/value/show', [CustomFieldController::class, 'getCustomFieldValues'])->name('custom-fields.value.show');
        Route::put('/{id}/value/edit', [CustomFieldController::class, 'updateCustomFieldValue'])->name('custom-fields.value.update');
        Route::delete('/{id}/value/{value}/delete', [CustomFieldController::class, 'deleteCustomFieldValue'])->name('custom-fields.value.delete');
    });
    Route::resource('custom-fields', CustomFieldController::class);
    /*** Custom Field Module : END ***/


    /*NOTE : Improve this mess of routes*/
    Route::group(['prefix' => 'seller-verification'], static function () {
        Route::put('/{id}/approval', [UserVerificationController::class, 'updateSellerApproval'])->name('seller_verification.approval');

        Route::get('/verification-requests', [UserVerificationController::class, 'show'])->name('verification_requests.show');
        Route::get('/verification-details/{id}', [UserVerificationController::class, 'getVerificationDetails']);
        //    Route::get('/user-report/show', [ReportReasonController::class, 'userReportsShow'])->name('report-reasons.user-reports.show');
        Route::put('/seller-verification/status-change', [UserVerificationController::class, 'updateStatus'])->name('seller-verification.update_status');
        Route::get('/verification-field/index', [UserVerificationController::class, 'verificationField'])->name('seller-verification.verification-field');
        Route::get('/verification-field', [UserVerificationController::class, 'showVerificationFields'])->name('verification-field.show');
        Route::get('/{id}/edit', [UserVerificationController::class, 'edit'])->name('seller-verification.verification-field.edit');
        Route::put('/{id}', [UserVerificationController::class, 'update'])->name('seller-verification.verification-field.update');
        Route::delete('/{id}/delete', [UserVerificationController::class, 'destroy'])->name('seller-verification.verification-field.delete');

        Route::post('/{id}/value/add', [UserVerificationController::class, 'addSellerVerificationValue'])->name('seller-verification.value.add');
        Route::get('/{id}/value/show', [UserVerificationController::class, 'getSellerVerificationValues'])->name('seller-verification.value.show');
        Route::put('/{id}/value/edit', [UserVerificationController::class, 'updateSellerVerificationValue'])->name('seller-verification.value.update');
        Route::delete('/{id}/value/{value}/delete', [UserVerificationController::class, 'deleteSellerVerificationValue'])->name('seller-verification.value.delete');
    });

    Route::resource('seller-verification', UserVerificationController::class);


    /*** Item Module : START ***/
    Route::group(['prefix' => 'item'], static function () {
        Route::match(['post', 'put'], '/{id}/approval', [ItemController::class, 'updateItemApproval'])->name('item.approval');
    });
    Route::resource('item', ItemController::class);
    /*** Item Module : END ***/

    Route::resource('seller-review', SellerController::class);
    Route::get('review-report', [SellerController::class, 'showReports'])->name('seller-review.report');


    /*** Setting Module : START ***/
    Route::group(['prefix' => 'settings'], static function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/store', [SettingController::class, 'store'])->name('settings.store');

        Route::get('system', [SettingController::class, 'page'])->name('settings.system');
        Route::get('about-us', [SettingController::class, 'page'])->name('settings.about-us.index');
        Route::get('privacy-policy', [SettingController::class, 'page'])->name('settings.privacy-policy.index');
        Route::get('contact-us', [SettingController::class, 'page'])->name('settings.contact-us.index');
        Route::get('terms-conditions', [SettingController::class, 'page'])->name('settings.terms-conditions.index');

        Route::get('firebase', [SettingController::class, 'page'])->name('settings.firebase.index');
        Route::post('firebase/update', [SettingController::class, 'updateFirebaseSettings'])->name('settings.firebase.update');

        Route::get('payment-gateway', [SettingController::class, 'paymentSettingsIndex'])->name('settings.payment-gateway.index');
        Route::post('payment-gateway', [SettingController::class, 'paymentSettingsStore'])->name('settings.payment-gateway.store');
        Route::get('language', [SettingController::class, 'page'])->name('settings.language.index');
        Route::get('admob', [SettingController::class, 'page'])->name('settings.admob.index');
        Route::get('/system-status', [SettingController::class, 'systemStatus'])->name('settings.system-status.index');
        Route::post('/toggle-storage-link', [SettingController::class, 'toggleStorageLink'])->name('toggle.storage.link');
        Route::get('error-logs', [LogViewerController::class, 'index'])->name('settings.error-logs.index');
        Route::get('seo-setting', [SettingController::class, 'page'])->name('settings.seo-settings.index');
        Route::get('file-manager', [SettingController::class, 'page'])->name('settings.file-manager.index');
        Route::get('web-settings', [SettingController::class, 'page'])->name('settings.web-settings');
        Route::get('notification-setting', [SettingController::class, 'page'])->name('settings.notification-setting');
        Route::post('file-manager-store', [SettingController::class, 'fileManagerSettingStore'])->name('settings.file-manager.store');

    });
    Route::group(['prefix' => 'system-update'], static function () {
        Route::get('/', [SystemUpdateController::class, 'index'])->name('system-update.index');
        Route::post('/', [SystemUpdateController::class, 'update'])->name('system-update.update');
    });
    /*** Setting Module : END ***/

    /*** Language Module : START ***/
    Route::group(['prefix' => 'language'], static function () {
        Route::get('set-language/{lang}', [LanguageController::class, 'setLanguage'])->name('language.set-current');
        Route::get('download/panel', [LanguageController::class, 'downloadPanelFile'])->name('language.download.panel.json');
        Route::get('download/app', [LanguageController::class, 'downloadAppFile'])->name('language.download.app.json');
        Route::get('download/web', [LanguageController::class, 'downloadWebFile'])->name('language.download.web.json');

        Route::put('/language/update/{id}/{type}', [LanguageController::class, 'updatelanguage'])->name('updatelanguage');
        Route::get('languageedit/{id}/{type}', [LanguageController::class, 'editLanguage'])->name('languageedit');
    });
    Route::resource('language', LanguageController::class);
    /*** Language Module : END ***/

    Route::resource('seo-setting', SeoSettingController::class);

    /*** User Module : START ***/
    Route::group(['prefix' => 'staff'], static function () {
        Route::put('/{id}/change-password', [StaffController::class, 'changePassword'])->name('staff.change-password');
    });
    Route::resource('staff', StaffController::class);

    /*** User Module : END ***/

    /*** Customer Module : START ***/
    Route::group(['prefix' => 'customer'], static function () {
        Route::post('/assign-package', [CustomersController::class, 'assignPackage'])->name('customer.assign.package');
        Route::get('/list-by-role', [CustomersController::class, 'listByRole'])->name('customer.list-by-role');
        Route::post('/toggle-status', [CustomersController::class, 'update'])->name('customer.toggle.status');
        Route::post('/update-client', [CustomersController::class, 'updateClient'])->name('customer.update-client');
        Route::get('/list', [CustomersController::class, 'show'])->name('customer.list');
        Route::delete('/delete/{id}', [CustomersController::class, 'destroy'])->name('customer.delete');
        Route::get('/get-category-names', [CustomersController::class, 'getCategoryNames'])->name('customer.get-category-names');
        
        // Featured users routes
        Route::post('/toggle-featured', [CustomersController::class, 'toggleFeatured'])->name('customer.toggle.featured');
        Route::resource('customer', CustomersController::class);
    });

    // Customer routes
    Route::get('customer', [CustomersController::class, 'index'])->name('customer.index');
    Route::get('clients', [CustomersController::class, 'clients'])->name('customer.clients');
    Route::get('business', [CustomersController::class, 'business'])->name('customer.business');
    Route::get('experts', [CustomersController::class, 'experts'])->name('customer.experts');

    /*** Customer Module : END ***/


    /*** Slider Module : START ***/
    Route::resource('slider', SliderController::class);
    /*** Slider Module : END ***/

    /*** Package Module : STARTS ***/
    Route::group(['prefix' => 'package'], static function () {
        Route::get('/advertisement', [PackageController::class, 'advertisementIndex'])->name('package.advertisement.index');
        Route::get('/advertisement/show', [PackageController::class, 'advertisementShow'])->name('package.advertisement.show');
        Route::post('/advertisement/store', [PackageController::class, 'advertisementStore'])->name('package.advertisement.store');
        Route::put('/advertisement/{id}/update', [PackageController::class, 'advertisementUpdate'])->name('package.advertisement.update');
        Route::get('/users/', [PackageController::class, 'userPackagesIndex'])->name('package.users.index');
        Route::get('/users/show', [PackageController::class, 'userPackagesShow'])->name('package.users.show');
        Route::get('/users/approve/{id}', [PackageController::class, 'approveUserPackage'])->name('package.users.approve');
        Route::get('/users/reject/{id}', [PackageController::class, 'rejectUserPackage'])->name('package.users.reject');
        Route::get('/payment-transactions/', [PackageController::class, 'paymentTransactionIndex'])->name('package.payment-transactions.index');
        Route::get('/payment-transactions/show', [PackageController::class, 'paymentTransactionShow'])->name('package.payment-transactions.show');
    });
    Route::resource('package', PackageController::class);
    /*** Package Module : ENDS ***/


    /*** Report Reason Module : START ***/
    Route::group(['prefix' => 'report-reasons'], static function () {
        Route::get('/user-report', [ReportReasonController::class, 'usersReports'])->name('report-reasons.user-reports.index');
        Route::get('/user-report/show', [ReportReasonController::class, 'userReportsShow'])->name('report-reasons.user-reports.show');
    });
    Route::resource('report-reasons', ReportReasonController::class);
    /*** Report Reason Module : END ***/


    /*** Notification Module : START ***/
    Route::group(['prefix' => 'notification'], static function () {
        Route::delete('/batch-delete', [NotificationController::class, 'batchDelete'])->name('notification.batch.delete');
    });
    Route::resource('notification', NotificationController::class);
    /*** Notification Module : END ***/


    /*** Feature Section Module : START ***/
    Route::resource('feature-section', FeatureSectionController::class);
    /*** Feature Section Module : END ***/


    /*** Roles Module : END ***/
    Route::get("/roles-list", [RoleController::class, 'list'])->name('roles.list');
    Route::resource('roles', RoleController::class);
    /*** Roles Module : END ***/

    /*** Tips Module : END ***/
    Route::resource('tips', TipController::class);
    /*** Tips Module : END ***/

    /*** Blog Module : END ***/
    Route::resource('blog', BlogController::class);
    /*** Blog Module : END ***/

    Route::resource('faq', FaqController::class);

    Route::group(['prefix' => 'countries'], static function () {
        Route::get("/", [PlaceController::class, 'countryIndex'])->name('countries.index');
        Route::get("/show", [PlaceController::class, 'countryShow'])->name('countries.show');
        Route::post("/import", [PlaceController::class, 'importCountry'])->name('countries.import');
        Route::delete("/{id}/delete", [PlaceController::class, 'destroyCountry'])->name('countries.destroy');
    });

    Route::group(['prefix' => 'states'], static function () {
        Route::get("/", [PlaceController::class, 'stateIndex'])->name('states.index');
        Route::get("/show", [PlaceController::class, 'stateShow'])->name('states.show');
        Route::get("/search", [PlaceController::class, 'stateSearch'])->name('states.search');
    });

    Route::group(['prefix' => 'cities'], static function () {
        Route::get("/", [PlaceController::class, 'cityIndex'])->name('cities.index');
        Route::get("/show", [PlaceController::class, 'cityShow'])->name('cities.show');
        Route::get("/search", [PlaceController::class, 'citySearch'])->name('cities.search');
    });
    /*** Area Module : START ***/
    Route::group(['prefix' => 'area'], static function () {
        Route::get('/', [PlaceController::class, 'createArea'])->name('area.index');
        Route::post('/create', [PlaceController::class, 'addArea'])->name('area.create');
        Route::get("/show/{id}", [PlaceController::class, 'areaShow'])->name('area.show');
        Route::put("/{id}/update-area", [PlaceController::class, 'updateArea'])->name('area.update');
        Route::delete("/{id}/delete-area", [PlaceController::class, 'destroyArea'])->name('area.destroy');
        Route::post('/create-city', [PlaceController::class, 'addCity'])->name('city.create');
        Route::put("/{id}/update", [PlaceController::class, 'updateCity'])->name('city.update');
        Route::delete("/{id}/delete", [PlaceController::class, 'destroyCity'])->name('city.destroy');
    });

    Route::group(['prefix' => 'contact-us'], static function () {
        Route::get('/', [Controller::class, 'contactUsUIndex'])->name('contact-us.index');
        Route::get('/show', [Controller::class, 'contactUsShow'])->name('contact-us.show');
    });
    /*** Area Module : END ***/
});
Route::get('/product-details/{slug}', [SettingController::class, 'webPageURL'])->name('deep-link');
Route::get('/migrate', static function () {
    Artisan::call('migrate');
    echo Artisan::output();
});

Route::get('/migrate-rollback', static function () {
    Artisan::call('migrate:rollback');
    echo "done";
});

Route::get('/seeder', static function () {
    Artisan::call('db:seed --class=SystemUpgradeSeeder');
    return redirect()->back();
});

Route::get('clear', static function () {
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('debugbar:clear');
    return redirect()->back();
});

Route::get('storage-link', static function () {
    Artisan::call('storage:link');
});

Route::get('auto-translate/{id}/{type}/{locale}', function ($id, $type, $locale) {
    Log::info("Running auto-translate with ID: $id, Type: $type, Locale: $locale");
    $exitCode = Artisan::call('custom:translate-missing', [
        'type' => $type,
        'locale' => $locale
    ]);
    if ($exitCode === 0) {
        Log::info("Auto translation completed successfully.");
        return redirect()->route('languageedit', ['id' => $id, 'type' => $type])
                         ->with('success', 'Auto translation completed successfully.');
    } else {
        Log::error("Auto translation failed with exit code: $exitCode");
        return redirect()->route('languageedit', ['id' => $id, 'type' => $type])
                         ->with('error', 'Auto translation failed.');
    }
})->name('auto-translate');

Route::get('delete-account', function () {
    return 'To delete your account, navigate to the app, login, go to profile, and press on delete account. This deletes all your data including listings, reviews ...';
});

Route::get('/test-log', function () {
    Log::info('Testing Laravel Log');

    return response()->json(['message' => 'Log test successful']);
});

// Service and Experience Item Management routes
Route::get('service-items', [ItemController::class, 'serviceItems'])->name('service.items.index');
Route::get('experience-items', [ItemController::class, 'experienceItems'])->name('experience.items.index');

// Data routes for service and experience items
Route::get('service-items/data', [ItemController::class, 'serviceItemsData'])->name('service.items.data');
Route::get('experience-items/data', [ItemController::class, 'experienceItemsData'])->name('experience.items.data');

// Keep your existing item routes
Route::get('item/{id}', [ItemController::class, 'showItem'])->name('item.show');
// Route::post('item/approval/{id}', [ItemController::class, 'updateItemApproval'])->name('item.approval');
Route::delete('item/{id}', [ItemController::class, 'destroy'])->name('item.destroy');