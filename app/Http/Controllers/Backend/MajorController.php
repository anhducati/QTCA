<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\Backend\MajorRequest;
use App\Models\Major;
use App\Models\AdmissionSubject;
use App\Models\ExamBlock;
use App\Http\Requests\Backend\MajorUpdateRequest;

class MajorController extends Controller
{
    
    public function major() {
        session()->flash('active', 'major');

        $data = [
            'listMajors' => Major::all(),
        ];

        return view('backend.major.list', $data);
    }


    public function create_major() {
        $data = [
            'examBlocks' => ExamBlock::all(),
        ];

        return view('backend.major.create', $data);
    }

    // public function handle_create_major(MajorRequest $request)
    // {
        
    //     $major = new Major();
    //     $major->name = trim($request->name);
    //     $major->slug = Str::slug($request->name, '-');

    //     $major->major_code = trim($request->major_code);
    //     $major->content = $request->contents;
    //     $major->save();

    //     if($request->hasFile('image_file')) {
    //         $ext = $request->file('image_file')->getClientOriginalExtension();
    //         $file = $request->file('image_file');
    //         // $fileName = $request->slug . '.' . $ext;

    //          $fileName = Str::slug($request->name, '-'). '-' . $major->id . '.' . $ext ;
    //         $uploadsPath = public_path('upload/major/');
    //         $file->move($uploadsPath , $fileName);

    //         $major->image_file = $fileName;
    //     }

    //     $major->save();

    //     AdmissionSubject::InsertDeleteSubjects($major->id, $request->subject_compo);
        

    //     return redirect()->route('admin.major')
    //         ->with('msg-success', 'Thêm ngành đào tạo thành công');
    // }

    public function handle_create_major(MajorRequest $request)
    {
        
        $major = new Major();
        $major->name = trim($request->name);
        $major->name_en = trim($request->name_en);
        $major->slug = Str::slug($request->name, '-');

        $major->major_code = trim($request->major_code);
        $major->content = $request->contents;
        $major->content_en = $request->contents_en;
        $major->save();

        if($request->hasFile('image_file')) {
            $ext = $request->file('image_file')->getClientOriginalExtension();
            $file = $request->file('image_file');
            // $fileName = $request->slug . '.' . $ext;

             $fileName = Str::slug($request->name, '-'). '-' . $major->id . '.' . $ext ;
            $uploadsPath = public_path('upload/major/');
            $file->move($uploadsPath , $fileName);

            $major->image_file = $fileName;
        }

        $major->save();

        AdmissionSubject::InsertDeleteSubjects($major->id, $request->subject_compo);
        

        return redirect()->route('admin.major')
            ->with('msg-success', 'Thêm ngành đào tạo thành công');
    }
    
    
    public function update_major ($id) {
        $major = Major::where('id', $id)->first();
        
        
        $data = [
            'major' => $major,
            'admissionSubject' => AdmissionSubject::getSubjectByMajorId($id),
            'examBlocks' => ExamBlock::all(),
        ];
        

        // dd(AdmissionSubject::getSubjectByMajorId($id));

        return view('backend.major.update', $data);
    }

    // public function handle_update_major (MajorUpdateRequest $request, $id) {
    //     $result = Major::updateMajor($request->all(), $id);


    //     if($result) {
    //         return redirect()->route('admin.major.update', $id)
    //             ->with('msg-success', 'Cập nhật ngành đào tạo thành công');
    //     }
        
    //     return redirect()->back()->with('msg-error', 'Cập nhật ngành đào tạo thất bại');
    // }
    public function handle_update_major (MajorUpdateRequest $request, $id) {
        $result = Major::updateMajor($request->all(), $id);


        if($result) {
            return redirect()->route('admin.major', $id)
                ->with('msg-success', 'Cập nhật ngành đào tạo thành công');
        }

        return redirect()->back()->with('msg-error', 'Cập nhật ngành đào tạo thất bại');
    }
    
    public function delete_major($id)
    {
        
        $major = Major::find($id);

        $result = $major->forceDelete();

        if ($result) {
            return redirect()->back()
                ->with('msg-success', "Ngành đào tạo đã được xóa vĩnh viễn");
        } else {
            return redirect()->back()
                ->with('msg-error', "Ngành đào tạo xóa không thành công");
        }

        

    }
}
