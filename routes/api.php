<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Authenticated Routes */
Route::group(['middleware' => ['auth:sanctum', 'auth.status', 'auth.optional']], static function () {
    Route::get('get-package', [ApiController::class, 'getPackage']);
    Route::post('update-profile', [ApiController::class, 'updateProfile']);
    Route::delete('delete-user', [ApiController::class, 'deleteUser']);

    Route::get('my-items', [ApiController::class, 'getItem']);
    Route::post('add-item', [ApiController::class, 'addItem']);
    Route::post('update-item', [ApiController::class, 'updateItem']);
    Route::post('delete-item', [ApiController::class, 'deleteItem']);
    Route::post('update-item-status', [ApiController::class, 'updateItemStatus']);
    Route::get('item-buyer-list', [ApiController::class, 'getItemBuyerList']);

    Route::post('renew-item', [ApiController::class, 'renewItem']);

    Route::post('assign-free-package', [ApiController::class, 'assignFreePackage']);
    Route::post('make-item-featured', [ApiController::class, 'makeFeaturedItem']);
    Route::post('manage-favourite', [ApiController::class, 'manageFavourite']);
    Route::post('add-reports', [ApiController::class, 'addReports']);
    Route::get('get-notification-list', [ApiController::class, 'getNotificationList']);
    Route::get('get-limits', [ApiController::class, 'getLimits']);
    Route::get('get-favourite-item', [ApiController::class, 'getFavouriteItem']);

    Route::get('get-payment-settings', [ApiController::class, 'getPaymentSettings']);
    Route::post('payment-intent', [ApiController::class, 'getPaymentIntent']);
    Route::get('payment-transactions', [ApiController::class, 'getPaymentTransactions']);

    /*Chat Module*/
    Route::post('item-offer', [ApiController::class, 'createItemOffer']);
    Route::get('chat-list', [ApiController::class, 'getChatList']);
    Route::post('send-message', [ApiController::class, 'sendMessage']);
    Route::get('chat-messages', [ApiController::class, 'getChatMessages']);

    Route::post('in-app-purchase', [ApiController::class, 'inAppPurchase']);

    Route::post('block-user', [ApiController::class, 'blockUser']);
    Route::post('unblock-user', [ApiController::class, 'unblockUser']);
    Route::get('blocked-users', [ApiController::class, 'getBlockedUsers']);

    Route::post('add-item-review', [ApiController::class, 'addItemReview']);
    Route::get('my-review', [ApiController::class, 'getMyReview']);
    Route::post('add-review-report', [ApiController::class, 'addReviewReport']);
    Route::get('user-has-rated-item', [ApiController::class, 'userHasRatedItem']);

    // New review endpoints
    Route::post('add-user-review', [ApiController::class, 'addUserReview']);
    Route::post('add-service-review', [ApiController::class, 'addServiceReview']);
    Route::get('user-has-rated-user', [ApiController::class, 'userHasRatedUser']);


    Route::get('verification-fields', [ApiController::class, 'getVerificationFields']);
    Route::post('send-verification-request',[ApiController::class,'sendVerificationRequest']);
    Route::get('verification-request',[ApiController::class,'getVerificationRequest']);

    // Featured users
    Route::post('make-user-featured', [ApiController::class, 'makeUserFeatured']);

    Route::get('get-item-authenticated', [ApiController::class, 'getItem']);
});

/* Non Authenticated Routes */
Route::middleware('auth.optional')->get('user-review', [ApiController::class, 'getUserReview']);
Route::get('get-provider', [ApiController::class, 'getProvider']);
Route::get('item-review', [ApiController::class, 'getItemReview']);
Route::post('login', [ApiController::class, 'login']);
Route::get('get-package', [ApiController::class, 'getPackage']);
Route::get('get-languages', [ApiController::class, 'getLanguages']);
Route::post('user-signup', [ApiController::class, 'userSignup']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('set-item-total-click', [ApiController::class, 'setItemTotalClick']);
Route::get('get-system-settings', [ApiController::class, 'getSystemSettings']);
Route::get('app-payment-status', [ApiController::class, 'appPaymentStatus']);
Route::get('get-customfields', [ApiController::class, 'getCustomFields']);
Route::get('get-item', [ApiController::class, 'getItem']);
Route::get('get-users', [ApiController::class, 'getUsers']);
Route::get('get-slider', [ApiController::class, 'getSlider']);
Route::get('get-report-reasons', [ApiController::class, 'getReportReasons']);
Route::get('get-categories', [ApiController::class, 'getSubCategories']);
Route::get('get-all-categories', [ApiController::class, 'getAllCategories']);
Route::get('get-parent-categories', [ApiController::class, 'getParentCategoryTree']);
Route::get('get-featured-section', [ApiController::class, 'getFeaturedSection']);
Route::get('blogs', [ApiController::class, 'getBlog']);
Route::get('blog-tags', [ApiController::class, 'getAllBlogTags']);
Route::get('faq', [ApiController::class, 'getFaqs']);
Route::get('tips', [ApiController::class, 'getTips']);
Route::get('countries', [ApiController::class, 'getCountries']);
Route::get('states', [ApiController::class, 'getStates']);
Route::get('cities', [ApiController::class, 'getCities']);
Route::get('areas', [ApiController::class, 'getAreas']);
Route::post('contact-us', [ApiController::class, 'storeContactUs']);
Route::get('seo-settings', [ApiController::class, 'seoSettings']);
Route::get('get-seller', [ApiController::class, 'getSeller']);
Route::get('experience-items', [ApiController::class, 'getExperienceItems']);
Route::get('exclusive-women-items', [ApiController::class, 'getExclusiveWomenItems']);
Route::get('corporate-package-items', [ApiController::class, 'getCorporatePackageItems']);
Route::get('newest-items', [ApiController::class, 'getNewestItems']);
Route::get('featured-items', [ApiController::class, 'getFeaturedItems']);
Route::get('featured-users', [ApiController::class, 'getFeaturedUsers']);

/* Google Places API Proxy Routes */
Route::get('search-places', [ApiController::class, 'searchPlaces']);
Route::get('place-details', [ApiController::class, 'getPlaceDetails']);
Route::get('reverse-geocode', [ApiController::class, 'reverseGeocode']);