<?php

use App\Http\Controllers\API\V1\AppSettingsController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\BacController;
use App\Http\Controllers\API\V1\BannerController;
use App\Http\Controllers\API\V1\ChangeEmailController;
use App\Http\Controllers\API\V1\ContentController;
use App\Http\Controllers\API\V1\ContentWebController;
use App\Http\Controllers\API\V1\EmailVerificationController;
use App\Http\Controllers\API\V1\FlashCardsController;
use App\Http\Controllers\API\V1\ForgotPasswordController;
use App\Http\Controllers\API\V1\LeaderBoardController;
use App\Http\Controllers\API\V1\SubscriptionController;
use App\Http\Controllers\API\V1\SummaryController;
use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;

// MENNADOS PEDADAA
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->summary('Register a new user')
            ->description('this endpoint takes the user name, email, phone number, and password and creates a new user, it also assigns the student role to the user, and returns the user data and a token');

        Route::post('login', [AuthController::class, 'login'])
            ->summary('Login a user')
            ->description('this endpoint takes the user email and password and logs the user in, it returns the user data and a token');

        Route::post('google/register', [AuthController::class, 'googleRegister'])
            ->summary('Register with Google')
            ->description('Registers (or updates) a user using a Google ID token and returns Sanctum access & refresh tokens.');
        Route::post('google/login', [AuthController::class, 'googleLogin'])
            ->summary('Login with Google')
            ->description('Authenticates a user via Google ID token, refreshes profile info, and issues new Sanctum tokens.');

        Route::post('check-email', [AuthController::class, 'checkEmail'])
            ->summary('Check if email exists')
            ->description('this endpoint checks if an email exists in the system');

        Route::post('check-phone-number', [AuthController::class, 'checkPhoneNumber'])
            ->summary('Check if phone number exists')
            ->description('this endpoint checks if a phone number exists in the system');

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Logout a user')
            ->description('this endpoint logs the user out and deletes the user token');

        Route::post('refresh-token', [AuthController::class, 'refreshToken'])
            ->middleware(['auth:sanctum', 'refresh'])
            ->summary('Refresh user token')
            ->description('this endpoint deletes the current user token and returns a new one');
    });

    Route::prefix('email')->group(function () {
        Route::post('send-verification-mail', [EmailVerificationController::class, 'sendVerificationMail'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Send verification email')
            ->description('Send a verification email with OTP to verify user email');

        // Route::post('/verify-otp', [EmailVerificationController::class, 'verifyOtp'])
        //     ->summary('Verify OTP')
        //     ->description('this endpoint takes the user email and the otp and verifies the otp');

        Route::post('verify-email', [EmailVerificationController::class, 'verifyEmail'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Verify user email')
            ->description('this endpoint takes the verification code and verifies the user email');

        // Route::post('unverify-me', [EmailVerificationController::class, 'unverifyMe'])
        //     ->middleware(['auth:sanctum', 'access'])
        //     ->summary('Unverify user email (testing)')
        //     ->description('this endpoint unverifies the user email (testing)');

        Route::post('change', [ChangeEmailController::class, 'changeEmail'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Request email change')
            ->description('This endpoint takes a new email address and sends a verification OTP to it');

        Route::post('verify-change', [ChangeEmailController::class, 'verifyChangeEmail'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Verify email change')
            ->description('This endpoint verifies the OTP sent to the new email and completes the email change process');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user infos')
            ->description('this endpoint returns the user data');

        Route::put('/', [UserController::class, 'updateUser'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Update user infos')
            ->description('this endpoint takes the user name, email, and phone number and updates the user data');

        Route::put('change-password', [UserController::class, 'updatePassword'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Change user password')
            ->description('this endpoint takes the old password and the new password and changes the user password');
    });

    Route::prefix('forget-password')->group(function () {
        Route::post('/', [ForgotPasswordController::class, 'forgotPassword'])
            ->summary('Forgot password')
            ->description('this endpoint takes the user email and sends a reset password mail');

        Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])
            ->summary('Verify OTP')
            ->description('this endpoint takes the user email and the otp and verifies the otp');

        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
            ->summary('Reset password')
            ->description('this endpoint takes the user email, the reset code, and the new password and resets the user password');
    });

    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'userSubscriptions'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user subscriptions')
            ->description('this returns an array of user subscriptions (id, name, description, ending_date)');

        Route::post('/redeem', [SubscriptionController::class, 'redeem'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Redeem a subscription card')
            ->description('this endpoint takes the code of the card and redeems it for the user, it errors if the card is already used by the user, if its used by another user, if the user already subscribed to the same subscription so there is no need to subscribe again');

        Route::post('/unsibscribe', [SubscriptionController::class, 'unsubscribe'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Unsubscribe from a subscription')
            ->description("this endpoint takes the subscription id and the user's password and unsubscribes the user from the subscription");
    });
    Route::prefix('content')->group(function () {
        Route::get('/', [ContentController::class, 'getUserContent'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Get user content')
            ->description('This endpoint returns the content associated with the authenticated user.');
        Route::post('/answer', [ContentController::class, 'SubmitChapterAnswers'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('Submit chapter answers')
            ->description('This endpoint submits the answers of a chapter.');
    });
    Route::prefix('leaderboard')->group(function () {
        Route::get('/', [LeaderBoardController::class, 'index'])
            ->middleware(['auth:sanctum', 'access'])
            ->summary('leader board')
            ->description("This endpoint returns a list of users paginated with query param 'page' and 'per_page', the list of users contains name, image, points.");
    });
    Route::prefix('settings')->group(function () {
        Route::get('/', [AppSettingsController::class, 'index'])
            ->summary('app settings')
            ->description('This endpoint returns an object containing app settings.');
    });

    // Banner Routes
    Route::get('banners', [BannerController::class, 'index'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('List all active banners')
        ->description('This endpoint returns all active banners');

    Route::get('banners/{id}', [BannerController::class, 'show'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Get banner by ID')
        ->description('This endpoint returns a specific active banner by its ID');

    // Summary Routes
    Route::get('summaries', [SummaryController::class, 'index'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('List all active summaries grouped by materials')
        ->description('This endpoint returns all active summaries grouped by materials with optional material filtering and pagination, simple example `/api/v1/summaries?per_page=20&page=1`, filter materials : `/api/v1/summaries?materials[]=1&materials[]=3`, note that the material ids must exist in the db');

    Route::get('summaries/{id}', [SummaryController::class, 'show'])->whereNumber('id')
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Get summary by ID')
        ->description('This endpoint returns a specific active summary by its ID');

    Route::get('summaries/content', [SummaryController::class, 'content'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Summaries content')
        ->description('Returns division materials and all active summaries as units.');

    // Bac Routes
    Route::get('bacs', [BacController::class, 'index'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('List all active bacs grouped by materials')
        ->description('This endpoint returns all active bacs grouped by materials with optional material filtering and pagination. Examples: `/api/v1/bacs?per_page=20&page=1` for pagination, `/api/v1/bacs?material_id=1` for single material filter, `/api/v1/bacs?materials[]=1&materials[]=3` for multiple materials filter. Note that the material IDs must exist in the database.');

    Route::get('bacs/{id}', [BacController::class, 'show'])->whereNumber('id')
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Get bac by ID')
        ->description('This endpoint returns a specific active bac by its ID');

    Route::get('bacs/content', [BacController::class, 'content'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Bacs content')
        ->description('Returns division materials and all active bacs as units.');

    // Flashcard Routes
    Route::get('flashcards/materials', [FlashCardsController::class, 'materialsWithFlashcardGroups'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('List materials with their flashcard groups and counts')
        ->description('This endpoint returns all materials that have flashcard groups, along with the flashcard groups for each material and the count of cards in each group. You can optionally filter by specific materials using `materials[]=1&materials[]=2` or `material_id=1` parameters. This is useful for displaying a structured overview of available flashcard content organized by materials and groups.');

    Route::get('flashcards', [FlashCardsController::class, 'index'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('List all flashcards with advanced filtering and pagination')
        ->description('This endpoint returns flashcards with comprehensive filtering and pagination support. You can filter by multiple materials using `materials[]=1&materials[]=2` or single material with `material_id=1`. You can also filter by multiple flashcard groups using `flashcard_groups[]=1&flashcard_groups[]=2` or single group with `flashcard_group_id=1`. Pagination is controlled with `per_page` (1-100, default 15) and `page` parameters. Examples: `/api/v1/flashcards?per_page=20&page=1` for pagination, `/api/v1/flashcards?materials[]=1&flashcard_groups[]=2&flashcard_groups[]=3` for advanced filtering. Each flashcard includes its content, parent group information, and associated material details.');

    Route::get('flashcards/{id}', [FlashCardsController::class, 'show'])->whereNumber('id')
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Get flashcard by ID')
        ->description('This endpoint returns a specific flashcard by its ID, including detailed information about the flashcard content, its parent flashcard group, and the associated material.');

    Route::get('flashcards/content', [FlashCardsController::class, 'content'])
        ->middleware(['auth:sanctum', 'access'])
        ->summary('Flashcards content')
        ->description("Returns topics (materials), categories (flashcard groups), and cards (flashcards) for the user's division.");

    // Web-optimized content routes
    Route::prefix('web')->middleware(['auth:sanctum', 'access'])->group(function () {
        Route::get('content', [ContentWebController::class, 'content'])
            ->summary('Web content snapshot')
            ->description('Lightweight content tree for the authenticated user. Includes paginated materials only. Use query params per_page (1-100, default 15) and page to paginate results.');

        Route::get('materials', [ContentWebController::class, 'materials'])
            ->summary('List my materials (paginated)')
            ->description('Returns the user-accessible materials for the current division, filtered by active subscriptions. Supports pagination via per_page (1-100, default 15) and page.');

        Route::get('materials/{materialId}/units', [ContentWebController::class, 'units'])
            ->whereNumber('materialId')
            ->summary('List units by material (paginated)')
            ->description('Returns units for the specified material if accessible to the user. Supports pagination via per_page (1-100, default 15) and page.');

        Route::get('units/{unitId}', [ContentWebController::class, 'unitWithChapters'])
            ->whereNumber('unitId')
            ->summary('Get unit with chapters')
            ->description('Returns unit data with all chapters at once. Includes progress, points and visibility for both unit and chapters.');

        Route::get('units/{unitId}/chapters', [ContentWebController::class, 'chapters'])
            ->whereNumber('unitId')
            ->summary('List chapters by unit (paginated)')
            ->description('Returns chapters for the specified unit if accessible to the user. Includes progress, points and visibility. Supports pagination via per_page (1-100, default 15) and page.');

        Route::get('chapters/{chapterId}/questions', [ContentWebController::class, 'questions'])
            ->whereNumber('chapterId')
            ->summary('List questions by chapter (paginated)')
            ->description('Returns transformed questions for the specified chapter. Supports pagination via per_page (1-100, default 15) and page.');
    });
});
