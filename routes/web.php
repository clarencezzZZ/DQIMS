 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontDeskController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ReportController;

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

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Monitor displays (public access)
Route::prefix('monitor')->name('monitor.')->group(function () {
    Route::get('/lobby', [MonitorController::class, 'lobby'])->name('lobby');
    Route::get('/lobby1', [MonitorController::class, 'lobby1'])->name('lobby1');
    Route::get('/lobby2', [MonitorController::class, 'lobby2'])->name('lobby2');
    Route::get('/third-floor', [MonitorController::class, 'thirdFloor'])->name('third-floor');
    Route::get('/queue-data', [MonitorController::class, 'queueData'])->name('queue-data');
    Route::get('/queue-data-lobby1', [MonitorController::class, 'queueDataLobby1'])->name('queue-data-lobby1');
    Route::get('/queue-data-lobby2', [MonitorController::class, 'queueDataLobby2'])->name('queue-data-lobby2');
    Route::get('/category-data/{category}', [MonitorController::class, 'categoryData'])->name('category-data');
    Route::get('/announcements', [MonitorController::class, 'announcements'])->name('announcements');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Front Desk Routes
    Route::middleware(['role:front_desk,admin'])->prefix('front-desk')->name('front-desk.')->group(function () {
        Route::get('/', [FrontDeskController::class, 'index'])->name('index');
        Route::get('/create', [FrontDeskController::class, 'create'])->name('create');
        Route::post('/store', [FrontDeskController::class, 'store'])->name('store');
        Route::get('/ticket/{inquiry}', [FrontDeskController::class, 'printTicket'])->name('ticket');
        Route::get('/queue-status', [FrontDeskController::class, 'queueStatus'])->name('queue-status');
        Route::get('/live-status', [FrontDeskController::class, 'showQueueStatus'])->name('live-status');
        Route::get('/recent-inquiries', [FrontDeskController::class, 'recentInquiries'])->name('recent-inquiries');
    });

    // Section Staff Routes
    Route::middleware(['role:section_staff,admin'])->prefix('section')->name('section.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::get('/test', function() {
            $user = auth()->user();
            return response()->json([
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'category_id' => $user->assigned_category_id,
                'category' => $user->assignedCategory ? $user->assignedCategory->name : null
            ]);
        })->name('test');
        Route::get('/waiting-list', [SectionController::class, 'waitingList'])->name('waiting-list');
        Route::get('/currently-serving', [SectionController::class, 'currentlyServing'])->name('currently-serving');
        Route::post('/call-next', [SectionController::class, 'callNext'])->name('call-next');
        Route::post('/complete', [SectionController::class, 'complete'])->name('complete');
        Route::post('/skip', [SectionController::class, 'skip'])->name('skip');
        Route::post('/forward', [SectionController::class, 'forwardToAdmin'])->name('forward');
        Route::get('/statistics', [SectionController::class, 'statistics'])->name('statistics');
    });

    // Admin Routes - Split for granular access control
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        // Inquiry Management - Allow adminfront
        Route::get('/inquiries', [AdminController::class, 'inquiries'])->name('inquiries');
        Route::post('/inquiries/update-status', [AdminController::class, 'updateInquiryStatus'])->name('inquiries.update-status');
        
        // Assessment Management - Allow adminfront
        Route::get('/assessments', [AdminController::class, 'assessments'])->name('assessments');
        Route::get('/assessments/{assessment}', [AdminController::class, 'showAssessment'])->name('assessments.show');
        Route::get('/assessments/{assessment}/edit', [AdminController::class, 'editAssessment'])->name('assessments.edit');
        Route::put('/assessments/{assessment}', [AdminController::class, 'updateAssessment'])->name('assessments.update');
        Route::get('/inquiries/{inquiry}/assessment/create', [AdminController::class, 'createAssessment'])->name('assessments.create');
        Route::post('/inquiries/{inquiry}/assessment', [AdminController::class, 'storeAssessment'])->name('assessments.store');
        Route::post('/assessments/store-direct', [AdminController::class, 'storeDirectAssessment'])->name('assessments.store-direct');
        Route::get('/assessments/last-number/{year}/{month}', [AdminController::class, 'getLastAssessmentNumber'])->name('assessments.last-number');
        Route::delete('/assessments/{assessment}', [AdminController::class, 'destroyAssessment'])->name('assessments.destroy');
        
        // User Management - Admin only (restrict from adminfront)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/users', [AdminController::class, 'users'])->name('users');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
            Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        });
        
        // Category Management - Admin only (restrict from adminfront)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
            Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
            Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        });
    });

    // Reports Routes (Admin only)
    Route::middleware(['role:admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/print', [ReportController::class, 'print'])->name('print');
    });

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isFrontDesk()) {
            return redirect()->route('front-desk.index');
        } elseif ($user->isSectionStaff()) {
            return redirect()->route('section.index');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.index');
        }
        
        return redirect('/');
    })->name('dashboard');
});

// API routes for section dashboard
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/categories', function() {
        $categories = \App\Models\Category::all();
        return response()->json($categories);
    })->name('api.categories');
});

// Auth routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
