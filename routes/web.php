 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontDeskController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SectionStaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

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

// Public front-desk queue status (for monitor display) - Also restricted for staff if enabled
Route::prefix('front-desk')->name('front-desk.')->middleware(['restricted.access'])->group(function () {
    Route::get('/queue-status', [FrontDeskController::class, 'queueStatus'])->name('queue-status');
    Route::get('/live-status', [FrontDeskController::class, 'showQueueStatus'])->name('live-status');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Diagnostic test page (for debugging)
    Route::get('/diagnostic-test', function() {
        return view('diagnostic-test');
    })->name('diagnostic-test');
    
    // API routes for section dashboard (inside auth middleware)
    Route::prefix('api')->group(function () {
        Route::get('/categories', function() {
            \Log::info('=== API Categories Request ===');
            \Log::info('User ID: ' . auth()->id());
            \Log::info('Username: ' . auth()->user()->username);
            
            $categories = \App\Models\Category::all();
            
            \Log::info('Categories count: ' . $categories->count());
            \Log::info('=== End API Categories Request ===');
            
            return response()->json($categories);
        })->name('api.categories');
    });
    
    // Front Desk Routes
    Route::middleware(['role:front_desk,admin', 'restricted.access'])->prefix('front-desk')->name('front-desk.')->group(function () {
        Route::get('/', [FrontDeskController::class, 'index'])->name('index');
        Route::get('/create', [FrontDeskController::class, 'create'])->name('create');
        Route::post('/store', [FrontDeskController::class, 'store'])->name('store');
        Route::get('/ticket/{inquiry}', [FrontDeskController::class, 'printTicket'])->name('ticket');
        Route::get('/recent-inquiries', [FrontDeskController::class, 'recentInquiries'])->name('recent-inquiries');
    });

    // Section Officer Routes (For officers without specific category assignment)
    Route::middleware(['role:section_officer,admin'])->prefix('section')->name('section.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::get('/debug', function() {
            return view('debug-section');
        })->name('debug');
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

    // Section Staff Routes (For staff with specific category assignment)
    Route::middleware(['role:section_staff'])->prefix('section-staff')->name('section-staff.')->group(function () {
        Route::get('/', [SectionStaffController::class, 'index'])->name('index');
        Route::get('/waiting-list', [SectionStaffController::class, 'waitingList'])->name('waiting-list');
        Route::get('/currently-serving', [SectionStaffController::class, 'currentlyServing'])->name('currently-serving');
        Route::post('/call-next', [SectionStaffController::class, 'callNext'])->name('call-next');
        Route::post('/complete', [SectionStaffController::class, 'complete'])->name('complete');
        Route::post('/skip', [SectionStaffController::class, 'skip'])->name('skip');
        Route::post('/forward', [SectionStaffController::class, 'forwardToAdmin'])->name('forward');
        Route::get('/statistics', [SectionStaffController::class, 'statistics'])->name('statistics');
    });

    // Admin Routes - Split for granular access control
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/modules/toggle', [AdminController::class, 'toggleModule'])->name('modules.toggle');
        
        // Inquiry Management - Allow adminfront
        Route::get('/inquiries', [AdminController::class, 'inquiries'])->name('inquiries');
        Route::post('/inquiries/update-status', [AdminController::class, 'updateInquiryStatus'])->name('inquiries.update-status');
        
        // Assessment Management - Allow adminfront
        Route::get('/assessments', [AdminController::class, 'assessments'])->name('assessments');
        Route::post('/assessment-types', [AdminController::class, 'storeAssessmentType'])->name('assessment-types.store');
        Route::put('/assessment-types/{type}', [AdminController::class, 'updateAssessmentType'])->name('assessment-types.update');
        Route::delete('/assessment-types/{type}', [AdminController::class, 'destroyAssessmentType'])->name('assessment-types.destroy');
        Route::get('/assessments/create-direct', function() {
            return view('admin.assessments');
        })->name('assessments.create-direct');
        Route::get('/assessments/{assessment}', [AdminController::class, 'showAssessment'])->name('assessments.show');
        Route::get('/assessments/{assessment}/download', [AdminController::class, 'downloadAssessment'])->name('assessments.download');
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
            Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
        });
    });

    // Reports Routes (Admin only)
    Route::middleware(['role:admin'])->prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/print', [ReportController::class, 'print'])->name('print');
    });
    
    // Profile Routes (All authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isFrontDesk()) {
            return redirect()->route('front-desk.index');
        } elseif ($user->isSectionStaff()) {
            return redirect()->route('section-staff.index');
        } elseif ($user->isSectionOfficer()) {
            return redirect()->route('section.index');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.index');
        }
        
        return redirect('/');
    })->name('dashboard');
});

// Auth routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
