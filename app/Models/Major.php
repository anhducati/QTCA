<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\AdmissionSubject;

class Major extends Model
{
    use HasFactory;

    protected $table = 'majors';

    public static function getAllMajorLimit($limit)
    {
        return Major::paginate($limit);
        //gọi random nghành học
        // return Major::inRandomOrder()->limit($limit)->get();
    }

    public static function getMajorBySly($slug)
    {
        $major = Major::where('slug', $slug)->first();
        if (!$major) {
            throw new \Exception("Major not found with slug '$slug'");
        }
        return $major;
    }

//     public static function updateMajor( array $data, $majorId)
//     {
//         // Tìm đối tượng Category cần cập nhật
//         $major = Major::findOrFail($majorId);

//         // Cập nhật thông tin của đối tượng Category từ dữ liệu được cung cấp
//         $major->name = $data['name'];
//         $major->slug = $data['slug'];
//         $major->major_code = $data['major_code'];
//         $major->content = $data['contents'];
//         $major->updated_at = date('d-m-Y H:i:s');
//         if(!empty($data['image_file'])) {
// //            $major->image_file = $data['image_file'];

//             $ext = $data['image_file']->getClientOriginalExtension();
//            $fileName = Str::slug($data['name'], '-'). '-' . $major->id . '.' . $ext ;
//             $uploadsPath = public_path('upload/major/');
//             $data['image_file']->move($uploadsPath, $fileName);

//             $major->image_file = $fileName;
//         }
        
//         // Lưu các thay đổi vào cơ sở dữ liệu và kiểm tra xem việc cập nhật thành công hay không
//         if ($major->save()) {
//             // Trả về true nếu cập nhật thành công
           
//             AdmissionSubject::InsertDeleteSubjects($major->id, $data['subject_compo']);
//             return true;
//         } else {
//             // Trả về false nếu có lỗi xảy ra khi cập nhật
//             return false;
//         }
//     }

    public static function updateMajor(array $data, $majorId)
    {
        // Tìm đối tượng Category cần cập nhật
        $major = Major::findOrFail($majorId);

        // Cập nhật thông tin của đối tượng Category từ dữ liệu được cung cấp
        $major->name = $data['name'];
        $major->name_en = $data['name_en'];
        $major->slug = $data['slug'];
        $major->major_code = $data['major_code'];
        $major->content = $data['contents'];
        $major->content_en = $data['contents_en'];
        $major->updated_at = date('Y-m-d H:i:s');
        if(!empty($data['image_file'])) {
    //            $major->image_file = $data['image_file'];

            $ext = $data['image_file']->getClientOriginalExtension();
        $fileName = Str::slug($data['name'], '-'). '-' . $major->id . '.' . $ext ;
            $uploadsPath = public_path('upload/major/');
            $data['image_file']->move($uploadsPath, $fileName);

            $major->image_file = $fileName;
        }

        // Lưu các thay đổi vào cơ sở dữ liệu và kiểm tra xem việc cập nhật thành công hay không
        if ($major->save()) {
            // Trả về true nếu cập nhật thành công
            
            AdmissionSubject::InsertDeleteSubjects($major->id, $data['subject_compo']);
            return true;
        } else {
            // Trả về false nếu có lỗi xảy ra khi cập nhật
            return false;
        }
    }

}
