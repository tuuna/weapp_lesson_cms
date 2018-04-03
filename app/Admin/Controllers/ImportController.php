<?php

namespace App\Admin\Controllers;

use App\Departs;
use App\Homework;
use App\Http\Controllers\Controller;
use App\Stulist;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ImportController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('学生名单');
            $content->description('上传学生名单');
            $content->body($this->form());
        });
    }

    public function form()
    {
        $teacher_id = Admin::user()->id;
        return Admin::form(Stulist::class, function (Form $form) use($teacher_id){
            $form->text('coursename','学科名');
            $form->text('row','行数')->placeholder('输入学生信息开始行数');
            $form->file('files','学生名单')->move('public/upload/xls');
            $form->hidden('teacherid')->value($teacher_id);
            $form->setAction('/admin/import/complete');
        });
    }

    public function complete(Request $request)
    {
        $course = $request->get('coursename');
        $row = $request->get('row');
        $file = $request->file('files');
        $teacherid = $request->get('teacherid');
        if ($file->isValid()) {
            // 获取文件相关信息
            $ext = $file->getClientOriginalExtension();     // 扩展名
            // 上传文件
            $filename = $course . '-' . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            $path = $file->storeAs('public', $filename);
        }
        if($path) {
            \Maatwebsite\Excel\Facades\Excel::load("/public/storage/".$filename,
                function($reader) use($row,$course,$teacherid) {

                $reader = $reader->getSheet(0);
                //获取表中的数据
                $results = $reader->toArray();
                $count = count($results);
                for($i = $row;$i<$count;$i++) {
                    if($results[$i][1] == '') {
                        break;
                    }
                    Stulist::create([
                        'coursename' => $course,
                        'stuid' => $results[$i][1],
                        'name' => $results[$i][2],
                        'teacherid' => $teacherid
                    ]);
                }
            });
        }
        echo "导入成功";
    }
}
