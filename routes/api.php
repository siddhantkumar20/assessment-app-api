<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssessmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Basic Routes
Route::get('/', [AssessmentController::class, 'usertype']);
Route::get('/admin', [AssessmentController::class, 'admin']);
Route::get('/adminregister', [AssessmentController::class, 'adminRegister']);
Route::get('/teacherlogin', [AssessmentController::class, 'teacherlogin']);
Route::get('/teacherregister', [AssessmentController::class, 'teacherregister']);
Route::get('/studentlogin', [AssessmentController::class, 'studentlogin']);
Route::get('/studentregister', [AssessmentController::class, 'studentregister']);
Route::get('/no-access', [AssessmentController::class, 'noAccess']);


// *********************** Admin Access *********************

// Admin Login/Logout
Route::post('/login-admin',[AssessmentController::class, 'loginAdmin'])->name('login-admin');
Route::get('/admin/{id}',[AssessmentController::class, 'adminDashboard']);
Route::get('/logout-admin/{id}', [AssessmentController::class, 'logoutAdmin'])->name('logout-admin');

//Admin Home Dashboard Buttons
Route::get('/admin/studentapproval/{id}',[AssessmentController::class, 'studentapproval'])->name('studentapproval');
Route::get('/admin/teacherapproval/{id}',[AssessmentController::class, 'teacherapproval'])->name('teacherapproval');

//Route to view
Route::get('/admin/studentapproval/view/{id}/{s_id}', [AssessmentController::class, 'viewStudent'])->name('view-student');
Route::get('/admin/teacherapproval/view/{id}/{t_id}', [AssessmentController::class, 'viewTeacher'])->name('view-teacher');

// Route to approve
Route::post('/approve-student/{id}/{s_id}', [AssessmentController::class, 'approveStudent'])->name('approve-student');
Route::post('/approve-teacher/{id}/{t_id}', [AssessmentController::class, 'approveTeacher'])->name('approve-teacher');

// Route to reject
Route::post('/reject-student/{id}/{s_id}', [AssessmentController::class, 'rejectStudent'])->name('reject-student');
Route::post('/reject-teacher/{id}/{t_id}', [AssessmentController::class, 'rejectTeacher'])->name('reject-teacher');

// Admin Registration
Route::post('/register-admin',[AssessmentController::class, 'registerAdmin'])->name('register-admin');

//********************** Admin Access End *******************

