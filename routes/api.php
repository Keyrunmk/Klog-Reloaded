<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Oauth\CallbackController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

// Passport
// Route::get("loginRedirect", RedirectController::class, "login");
// Route::get("callback", CallbackController::class);

// Home
Route::get("/", [HomeController::class, "index"]);

//login and registration
Route::post("login", [AuthenticationController::class, "login"]);
Route::post("register", [AuthenticationController::class, "register"]);
Route::post("verify-email/{user_id}", [AuthenticationController::class, "verify"]);
Route::post("logout", [AuthenticationController::class, "logout"])->middleware("auth:api");
Route::post("refresh", [AuthenticationController::class, "refreshToken"])->middleware("auth:api");
Route::post("otp/verify", [AuthenticationController::class, "verifyOtp"]);

Route::middleware("auth:api", "role:user", "verify:active")->group(function () {
    //profile
    Route::prefix("profile")->group(function () {
        Route::get("{profile_id}", [ProfileController::class, "show"]);
        Route::patch("{profile_id}", [ProfileController::class, "update"]);
        Route::post("{profile_id}/follow", [ProfileController::class, "follow"]);
    });

    //posts
    Route::apiResource("post", PostController::class);
    Route::prefix("post")->group(function () {
        // Report Post
        Route::post("{post_id}/report", [PostController::class, "report"]);

        //comments
        Route::post("{post_id}/comment", [PostController::class, "comment"]);
        Route::delete("{post_id}/comment/{comment_id}", [PostController::class, "deleteComment"]);

        //tags
        Route::post("{post_id}/tag", [PostController::class, "tagPost"]);
    });
});

Route::fallback(function () {
    return response()->json([
        "message" => "Page Not Found. If error persists, contact info@klog.com"
    ], Response::HTTP_NOT_FOUND);
});
