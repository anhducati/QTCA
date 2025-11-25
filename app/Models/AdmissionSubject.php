<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class AdmissionSubject extends Model
// {
//     use HasFactory;

//     protected $table = 'admission_subjects';

//     public static function InsertDeleteSubjects($majorId, $name)
//     {
//         AdmissionSubject::where('major_id', '=', $majorId)->delete();

//         if(is_array($name)) {
//             $subjectArray = $name;

//             foreach ($subjectArray as $name) {
//                 $save = new AdmissionSubject();
//                 $save->major_id = $majorId;
//                 $save->name = $name;
//                 $save->save();
//             }
//         }
//     }

//     public static function getSubjectByMajorId($majorId)
//     {
//         return AdmissionSubject::where('major_id', $majorId)
//             ->get();
        
//     }
// }
class AdmissionSubject extends Model
{
    use HasFactory;

    protected $table = 'admission_subjects';

    public static function InsertDeleteSubjects($majorId, $id)
    {
        AdmissionSubject::where('major_id', '=', $majorId)->delete();

        if(is_array($id)) {
                $id_exam_block = $id;
            foreach ($id_exam_block as $id) {
                $save = new AdmissionSubject();
                $save->major_id = $majorId;
                $save->exam_block_id = $id;
                $save->save();
            }
        }
    }

    public static function getSubjectByMajorId($majorId)
    {
        return AdmissionSubject::where('major_id', $majorId)
            ->get();
        
    }
    // Trong model AdmissionSubject.php

// Trong model AdmissionSubject.php

// public static function updateSubjects($majorId, $subjectCompo)
// {
//     // Xóa hết các bản ghi có major_id tương ứng
//     AdmissionSubject::where('major_id', '=', $majorId)->delete();

//     // Thêm mới các bản ghi từ $subjectCompo
//     if (!empty($subjectCompo)) {
//         foreach ($subjectCompo as $examBlockId) {
//             $subject = new AdmissionSubject();
//             $subject->major_id = $majorId;
//             $subject->exam_block_id = $examBlockId;
//             $subject->save();
//         }
//     }
// }


    // public static function getSubjectByExamBlockId($exam_block_id)
    // {
    //     return AdmissionSubject::where('exam_block_id', $exam_block_id)
    //         ->get();
        
    // }
}