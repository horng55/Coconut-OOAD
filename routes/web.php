<?php

use App\Http\Controllers\Web\Admin\Auth\AuthController;
use App\Http\Controllers\Web\Admin\Auth\RegisteredController;
use App\Http\Controllers\Web\Admin\Banner\BannerController;
use App\Http\Controllers\Web\Admin\Brand\BrandController;
use App\Http\Controllers\Web\Admin\Category\CategoriesController;
use App\Http\Controllers\Web\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Web\Admin\Post\PostController;
use App\Http\Controllers\Web\Admin\Product\ProductController;
use App\Http\Controllers\Web\Admin\Province\GoogleLocationController;
use App\Http\Controllers\Web\Admin\Province\ProvinceController;
use App\Http\Controllers\Web\Admin\Service\ServiceController;
use App\Http\Controllers\Web\Admin\Slideshow\SlideShowController;
use App\Http\Controllers\Web\Admin\SubCategory\SubCategoriesController;
use App\Http\Controllers\Web\Admin\Subscription\AdminPlanController;
use App\Http\Controllers\Web\Admin\Subscription\AdminSubscriptionController;
use App\Http\Controllers\Web\Admin\Subscription\AdminUserSubscriptionController;
// use App\Http\Controllers\Web\Admin\Security\LoginLogController;
use App\Http\Controllers\Web\Admin\User\UserController;
use App\Http\Controllers\Web\Admin\Teacher\TeacherController;
use App\Http\Controllers\Web\Admin\Student\StudentController;

use App\Http\Controllers\Web\Admin\Class\ClassController;
use App\Http\Controllers\Web\Admin\Enrollment\EnrollmentController;

use App\Http\Controllers\Web\Admin\Grade\GradeController;


use App\Http\Controllers\Web\Admin\IdCard\IdCardController;
use App\Http\Controllers\Web\Admin\Promotion\PromotionController as AdminPromotionController;

use App\Http\Controllers\Web\Admin\Role\RoleController;
use App\Http\Controllers\Web\Admin\Fee\FeeController;
use App\Http\Controllers\Web\Admin\Event\EventController as AdminEventController;
use App\Http\Controllers\Web\Admin\Guest\GuestController as AdminGuestController;
use App\Http\Controllers\Web\Front\About\AboutController;
use App\Http\Controllers\Web\Front\Contact\ContactController;
use App\Http\Controllers\Web\Front\FrontController;
use App\Http\Controllers\Web\Front\Function\FunctionController;
use App\Http\Controllers\Web\Front\Privacy\PrivacyController;
use App\Http\Controllers\Web\Front\ShowcaseController;
use App\Http\Controllers\Web\Front\Promotion\PromotionController as FrontPromotionController;
use App\Http\Controllers\Web\User\Auth\UserAuthController;
use App\Http\Controllers\Web\User\Auth\UserEmailVerifiedController;
use App\Http\Controllers\Web\User\Dashboard\UserDashboardController;
use App\Http\Controllers\Web\User\Event\UserEventController;
use App\Http\Controllers\Web\User\Guest\UserGuestController;
use App\Http\Controllers\Web\User\Invite\UserInviteController;
use App\Http\Controllers\Web\User\Setting\UserSettingController;
use App\Http\Controllers\Web\User\Subscription\UserSubscriptionController;
use App\Http\Controllers\Web\Student\Auth\StudentAuthController;

use App\Http\Middleware\CheckUserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Homepage - Shows login options
Route::get('/', [\App\Http\Controllers\Web\Front\FrontController::class, 'index'])->name('home');

// Language switching
Route::post('/language/switch', [\App\Http\Controllers\Web\LanguageController::class, 'switch'])->name('language.switch');

// Frontend Routes

Route::prefix('user')->group(function () {
    Route::get('/login', [UserAuthController::class, 'create'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.store');

    Route::get('/register', [UserAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('register.store');

    Route::middleware('user')->group(function () {
        // Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

        // Route::get('/setting', [UserSettingController::class, 'index'])->name('setting');
        // Route::post('/setting', [UserSettingController::class, 'save'])->name('setting.save');
    });
});

// Teacher Authentication Routes
Route::prefix('teacher')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Web\Teacher\Auth\TeacherAuthController::class, 'create'])->name('teacher.login');
    Route::post('/login', [\App\Http\Controllers\Web\Teacher\Auth\TeacherAuthController::class, 'login'])->name('teacher.login.store');

    Route::middleware(['auth', \App\Http\Middleware\TeacherHandleInertiaRequests::class])->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Web\Teacher\Auth\TeacherAuthController::class, 'logout'])->name('teacher.logout');
        Route::get('/dashboard', [\App\Http\Controllers\Web\Teacher\Dashboard\TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        
        // Teacher Grades Management
        Route::get('/grades', [\App\Http\Controllers\Web\Teacher\Grade\TeacherGradeController::class, 'index'])->name('teacher.grades.index');
        Route::get('/grades/create', [\App\Http\Controllers\Web\Teacher\Grade\TeacherGradeController::class, 'create'])->name('teacher.grades.create');
        Route::post('/grades', [\App\Http\Controllers\Web\Teacher\Grade\TeacherGradeController::class, 'store'])->name('teacher.grades.store');
        Route::get('/grades/{id}/edit', [\App\Http\Controllers\Web\Teacher\Grade\TeacherGradeController::class, 'edit'])->name('teacher.grades.edit');
        Route::put('/grades/{id}', [\App\Http\Controllers\Web\Teacher\Grade\TeacherGradeController::class, 'update'])->name('teacher.grades.update');
        
        // Teacher Assessment Management
        Route::get('/assessments', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'index'])->name('teacher.assessments.index');
        Route::get('/assessments/create', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'create'])->name('teacher.assessments.create');
        Route::post('/assessments', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'store'])->name('teacher.assessments.store');
        Route::get('/assessments/{id}', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'show'])->name('teacher.assessments.show');
        Route::get('/assessments/{id}/edit', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'edit'])->name('teacher.assessments.edit');
        Route::put('/assessments/{id}', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'update'])->name('teacher.assessments.update');
        Route::delete('/assessments/{id}', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'destroy'])->name('teacher.assessments.destroy');
        Route::post('/assessments/grades/{id}', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'updateGrade'])->name('teacher.assessments.update-grade');
        Route::get('/assessments/submissions/{submissionId}/download', [\App\Http\Controllers\Web\Teacher\Assessment\TeacherAssessmentController::class, 'downloadSubmission'])->name('teacher.assessments.download-submission');
        
        // Teacher Assignment Management
        Route::get('/assignments', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'index'])->name('teacher.assignments.index');
        Route::get('/assignments/create', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'create'])->name('teacher.assignments.create');
        Route::post('/assignments', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'store'])->name('teacher.assignments.store');
        Route::get('/assignments/{id}', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'show'])->name('teacher.assignments.show');
        Route::get('/assignments/{id}/edit', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'edit'])->name('teacher.assignments.edit');
        Route::post('/assignments/{id}', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'update'])->name('teacher.assignments.update');
        Route::delete('/assignments/{id}', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'destroy'])->name('teacher.assignments.destroy');
        Route::post('/assignments/{assignment}/submissions/{submission}/grade', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'gradeSubmission'])->name('teacher.assignments.grade-submission');
        Route::get('/assignments/{id}/download', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'downloadAttachment'])->name('teacher.assignments.download-attachment');
        Route::get('/assignments/{assignment}/submissions/{submission}/download', [\App\Http\Controllers\Web\Teacher\Assignment\TeacherAssignmentController::class, 'downloadSubmission'])->name('teacher.assignments.download-submission');
        
        // Teacher Classes
        Route::get('/classes', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'index'])->name('teacher.classes.index');
        Route::get('/classes/create', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'create'])->name('teacher.classes.create');
        Route::post('/classes', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'store'])->name('teacher.classes.store');
        Route::get('/classes/{id}', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'show'])->name('teacher.classes.show');
        Route::get('/classes/{id}/create-student', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'createStudent'])->name('teacher.classes.create-student');
        Route::post('/classes/{id}/students', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'storeStudent'])->name('teacher.classes.store-student');
        Route::get('/classes/{id}/enroll-student', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'enrollStudent'])->name('teacher.classes.enroll-student');
        Route::post('/classes/{id}/enroll', [\App\Http\Controllers\Web\Teacher\Class\TeacherClassController::class, 'storeEnrollment'])->name('teacher.classes.store-enrollment');
        
        // Teacher Students
        Route::get('/students', [\App\Http\Controllers\Web\Teacher\Student\TeacherStudentController::class, 'index'])->name('teacher.students.index');
        Route::get('/students/{id}', [\App\Http\Controllers\Web\Teacher\Student\TeacherStudentController::class, 'show'])->name('teacher.students.show');
    });
});

// Student Authentication Routes
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'create'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.store');

    Route::middleware(['auth', \App\Http\Middleware\StudentHandleInertiaRequests::class])->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
        Route::get('/dashboard', [\App\Http\Controllers\Web\Student\Dashboard\StudentDashboardController::class, 'index'])->name('student.dashboard');
        
        // Student Assessments
        Route::get('/assessments', [\App\Http\Controllers\Web\Student\Assessment\StudentAssessmentController::class, 'index'])->name('student.assessments.index');
        Route::get('/assessments/{id}', [\App\Http\Controllers\Web\Student\Assessment\StudentAssessmentController::class, 'show'])->name('student.assessments.show');
        Route::post('/assessments/{id}/submit', [\App\Http\Controllers\Web\Student\Assessment\StudentAssessmentController::class, 'submit'])->name('student.assessments.submit');
        Route::get('/assessments/{id}/download-submission', [\App\Http\Controllers\Web\Student\Assessment\StudentAssessmentController::class, 'downloadSubmission'])->name('student.assessments.download-submission');
        
        // Student Assignments
        Route::get('/assignments', [\App\Http\Controllers\Web\Student\Assignment\StudentAssignmentController::class, 'index'])->name('student.assignments.index');
        Route::get('/assignments/{id}', [\App\Http\Controllers\Web\Student\Assignment\StudentAssignmentController::class, 'show'])->name('student.assignments.show');
        Route::post('/assignments/{id}/submit', [\App\Http\Controllers\Web\Student\Assignment\StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
        Route::get('/assignments/{id}/download', [\App\Http\Controllers\Web\Student\Assignment\StudentAssignmentController::class, 'downloadAttachment'])->name('student.assignments.download-attachment');
        
        // Student Grades
        Route::get('/grades', [\App\Http\Controllers\Web\Student\Grade\StudentGradeController::class, 'index'])->name('student.grades.index');
        
        // Student Classes
        Route::get('/classes', [\App\Http\Controllers\Web\Student\Class\StudentClassController::class, 'index'])->name('student.classes.index');
        Route::get('/classes/{id}', [\App\Http\Controllers\Web\Student\Class\StudentClassController::class, 'show'])->name('student.classes.show');
    });
});



Route::prefix('admin')->group(function () {

    // Admin Authentication Routes
    Route::get('/login', [AuthController::class, 'create'])->name('admin.index');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.store');

//    Route::get('/register', [RegisteredController::class, 'create'])->name('admin.register.index');
//    Route::post('/register', [RegisteredController::class, 'store'])->name('admin.register.store');

    // Admin Authenticated Routes (using auth and admin middleware)
    Route::middleware(['auth', 'admin'])->group(function () {
        // Dashboard Route
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // UserSidebar Management Routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/user/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'delete'])->name('users.delete');

        // Teachers Management
        Route::resource('teachers', TeacherController::class)->names([
            'index' => 'admin.teachers.index',
            'create' => 'admin.teachers.create',
            'store' => 'admin.teachers.store',
            'show' => 'admin.teachers.show',
            'edit' => 'admin.teachers.edit',
            'update' => 'admin.teachers.update',
            'destroy' => 'admin.teachers.destroy',
        ]);

        // Students Management
        Route::resource('students', StudentController::class)->names([
            'index' => 'admin.students.index',
            'create' => 'admin.students.create',
            'store' => 'admin.students.store',
            'show' => 'admin.students.show',
            'edit' => 'admin.students.edit',
            'update' => 'admin.students.update',
            'destroy' => 'admin.students.destroy',
        ]);

        // Subjects Management
        Route::resource('subjects', \App\Http\Controllers\Web\Admin\Subject\SubjectController::class)->names([
            'index' => 'admin.subjects.index',
            'create' => 'admin.subjects.create',
            'store' => 'admin.subjects.store',
            'show' => 'admin.subjects.show',
            'edit' => 'admin.subjects.edit',
            'update' => 'admin.subjects.update',
            'destroy' => 'admin.subjects.destroy',
        ]);

        // Classes Management
        Route::resource('classes', ClassController::class)->names([
            'index' => 'admin.classes.index',
            'create' => 'admin.classes.create',
            'store' => 'admin.classes.store',
            'show' => 'admin.classes.show',
            'edit' => 'admin.classes.edit',
            'update' => 'admin.classes.update',
            'destroy' => 'admin.classes.destroy',
        ]);

        // Grades Management
        Route::resource('grades', GradeController::class)->names([
            'index' => 'admin.grades.index',
            'create' => 'admin.grades.create',
            'store' => 'admin.grades.store',
            'show' => 'admin.grades.show',
            'edit' => 'admin.grades.edit',
            'update' => 'admin.grades.update',
            'destroy' => 'admin.grades.destroy',
        ]);

        // Assignments Management (renamed from Assessments)
        Route::resource('assignments', \App\Http\Controllers\Web\Admin\Assessment\AssessmentController::class)->names([
            'index' => 'admin.assessments.index',
            'create' => 'admin.assessments.create',
            'store' => 'admin.assessments.store',
            'show' => 'admin.assessments.show',
            'edit' => 'admin.assessments.edit',
            'update' => 'admin.assessments.update',
            'destroy' => 'admin.assessments.destroy',
        ]);

        // Logout Route
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
