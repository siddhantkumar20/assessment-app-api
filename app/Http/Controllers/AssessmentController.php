<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Approval;
use App\Models\ApprovalStudent;

use App\Mail\ApprovedStudent;
use App\Mail\RejectedStudent;
use App\Mail\ApprovedTeacher;
use App\Mail\RejectedTeacher;
use Illuminate\Support\Facades\Notification;

use App\Notifications\NewStudent;

use Mail;
use md5;
use Session;

class AssessmentController extends Controller
{
    // User Type Page
    function usertype()
    {
        return ["Page"=>"UserType"]; 
    }

    //Admin Login Page
    function admin()
    {
        return ["Page"=>"Admin Login Page"]; 
    }

    //Admin Registration Page
    function adminRegister()
    {
        return ["Page"=>"Admin Registration Page"]; 
    }

    // Teacher Login Page
    function teacherlogin()
    {
        return ["Page"=>"Teacher Login Page"]; 
    }

    //Teacher Registration Page
    function teacherregister()
    {
        return ["Page"=>"Teacher Registration Page"]; 
    }

    //Student Login Page
    function studentlogin()
    {
        return ["Page"=>"Student Login Page"]; 
    }

    //Student Registration Page
    function studentregister()
    {
        return ["Page"=>"Student Registration Page"]; 
    }

    // Error Page
    function noAccess()
    {
        return ["Page"=>"Not Authorized"]; 
    }

    // Admin Login Functionality
    function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        $admin = Admin::where('email','=',$request->email)->first();

        if (!$admin) 
        {
            return response()->json([
                'status' => 404,
                'message' => "Not Registered"
            ],404);
        }
        else 
        {
            if(md5($request->password) === ($admin->password))
            {
                return response()->json([
                'status' => 200,
                'message' => "Login Successful",
                'redirect' => "admin/".$admin->id,
                ], 200);
            }
            
            else
            {
                return response()->json([
                'status' => 404,
                'message' => "Incorrect Password"
                ],404);
            }
        }
    }

    // Admin Logout
    function logoutAdmin($id)
    {
        $admin = Admin::find($id);
        
        return response()->json([
            'status' => 200,
            'admin' => $admin,
            'message' => "Logged Out Successfully"
            ],200);
    }

    // Admin Dashboard
    function adminDashboard($id)
    {
        $admin = Admin::find($id);
    
        if (!$admin) {
            return response()->json([
                'status' => 404,
                'message' => 'Admin not found',
            ], 404);
        }
    
        return response()->json([
            'status' => 200,
            'admin' => $admin,
            'student approval' => "admin/studentapproval/".$admin->id,
            'teacher approval' => "admin/teacherapproval/".$admin->id,
        ], 200);
    }

    // Student Approval
    function studentapproval($id){
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
            'status' => 404,
            'message' => 'Admin not found',
            ], 404);
        }else{
            $approval = ApprovalStudent::all();
            if($approval->count()>0)
            {
                return response()->json([
                    'status' => 200,
                    'admin' => $admin,
                    'students approvals' => $approval,
                    'redirect' => "admin/studentapproval/view/".$admin->id."/approval-id"
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 200,
                    'message' => "No Students Approvals"
                ]);
            }
        }
    }
    
    // Student View 
    function viewStudent($id, $s_id){
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
            'status' => 404,
            'message' => 'Admin not found',
            ], 404);
        }else{
            $student = ApprovalStudent::find($s_id);
            if(!$student){
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                    ], 404);
            }else{
                return response()->json([
                    'status' => 200,
                    'admin' => $admin,
                    'student' => $student,
                    'approve' => 'approve-student/'.$admin->id."/approval id",
                    'reject' => 'reject-student/'.$admin->id."/approval id"
                ], 200);
            }
        }
    }

    // Student Approved
    function approveStudent($id, $s_id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ]);

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 404,
                'message' => 'Admin not found',
            ], 404);
        } else {
            $student = ApprovalStudent::find($s_id);

            if (!$student) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                ], 404);
            } else {
                

                $newStudent = new Student;

                // Assign values from $student to $newStudent
                $newStudent->name = $student->name;
                $newStudent->email = $student->email;
                $newStudent->address = $student->address;
                $newStudent->image = $student->image;
                $newStudent->cs = $student->cs;
                $newStudent->ps = $student->ps;
                $newStudent->parent = $student->parent;
                $newStudent->parentno = $student->parentno;
                $newStudent->password = $student->password;

                try {
                    $res = $newStudent->save();
                    ApprovalStudent::where('id', $s_id)->delete();
                    Mail::to($newStudent->email)->send(new ApprovedStudent());
                    return response()->json([
                        'status' => 200,
                        'message' => 'Approved and Mail sent Successfully',
                    ], 200);
                }catch (\Exception $e) {
                    ApprovalStudent::where('id', $s_id)->delete();
                        return response()->json([
                            'status' => 500, // Change status to 500 for server error
                            'message' => 'Something went wrong!!!'
                        ], 500);
                }
            }
        }
    }

    // Reject Student
    function rejectStudent($id, $s_id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ]);

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 404,
                'message' => 'Admin not found',
            ], 404);
        } else {
            $student = ApprovalStudent::find($s_id);

            if (!$student) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                ], 404);
            } else {

                try {
                    Mail::to($student->email)->send(new RejectedStudent());
                    ApprovalStudent::where('id',$s_id)->delete();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Rejected and Mail sent Successfully',
                    ], 200);

                } catch (\Exception $e) {
                    ApprovalStudent::where('id',$s_id)->delete();
                    return response()->json([
                        'status' => 500,
                        'message' => 'Something went wrong!!!'
                    ], 500);
                }
            }
        }
    }

    // Teacher Approval
    function teacherapproval($id){
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
            'status' => 404,
            'message' => 'Admin not found',
            ], 404);
        }else{
            $approval = Approval::all();
            if($approval->count()>0)
            {
                return response()->json([
                    'status' => 200,
                    'admin' => $admin,
                    'teacher approvals' => $approval,
                    'redirect' => "admin/teacherapproval/view/".$admin->id."/approval-id"
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 200,
                    'message' => "No Teachers Approvals"
                ]);
            }
        }
    }

    // Teacher View 
    function viewTeacher($id, $t_id){
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
            'status' => 404,
            'message' => 'Admin not found',
            ], 404);
        }else{
            $teacher = Approval::find($t_id);
            if(!$teacher){
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                    ], 404);
            }else{
                return response()->json([
                    'status' => 200,
                    'admin' => $admin,
                    'teacher' => $teacher,
                    'approve' => 'approve-teacher/'.$admin->id."/approval id",
                    'reject' => 'reject-teacher/'.$admin->id."/approval id"
                ], 200);
            }
        }
    }



    // Teacher Approved
    function approveTeacher($id, $t_id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ]);

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 404,
                'message' => 'Admin not found',
            ], 404);
        } else {
            $teacher = Approval::find($t_id);

            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                ], 404);
            } else {
                $newteacher = new Teacher;
                $newteacher->name = $teacher->name;
                $newteacher->email = $teacher->email;
                $newteacher->address = $teacher->address;
                $newteacher->image = $teacher->image;
                $newteacher->cs = $teacher->cs;
                $newteacher->ps = $teacher->ps;
                $newteacher->experience = $teacher->experience;
                $newteacher->expertise = $teacher->expertise;
                $newteacher->password = $teacher->password;

                try {
                    $res = $newteacher->save();
                    Approval::where('id', $t_id)->delete();
                    
                    Mail::to($newteacher->email)->send(new ApprovedTeacher());
                    return response()->json([
                        'status' => 200,
                        'message' => 'Approved and Mail sent Successfully',
                    ], 200);
                }catch (\Exception $e) {
                    Approval::where('id', $t_id)->delete();
                        return response()->json([
                            'status' => 500, // Change status to 500 for server error
                            'message' => 'Something went wrong!!!'
                        ], 500);
                }
            }
        }
    }

    // Reject Teacher
    function rejectTeacher($id, $t_id, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ]);

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 404,
                'message' => 'Admin not found',
            ], 404);
        } else {
            $teacher = Approval::find($t_id);

            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Approval not found',
                ], 404);
            } else {

                try {
                    Mail::to($teacher->email)->send(new RejectedTeacher());
                    Approval::where('id',$t_id)->delete();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Rejected and Mail sent Successfully',
                    ], 200);

                } catch (\Exception $e) {
                    Approval::where('id',$t_id)->delete();
                    return response()->json([
                        'status' => 500,
                        'message' => 'Something went wrong!!!'
                    ], 500);
                }
            }
        }
    }

    // Admin Registration Functionality
    function registerAdmin(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:admins',
            'password'=>'required|min:6'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ],422);
        }
        else
        {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => md5($request->password)
            ]);
        }

        if($admin){
            return response()->json([
                'status' => 200,
                'message' => "You have been registered"
            ],200);
        }
        else{
            return response()->json([
                'status' => 500,
                'message' => "Something went Wrong"
            ],500);
        }
    }
}
