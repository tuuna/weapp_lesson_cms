<?php

namespace App\Admin\Controllers;

use App\Departs;
use App\Homework;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('新增活动');
            $content->description('新增活动页面');
            $content->body($this->form());
        });
    }

    public function form()
    {
        $teacher_id = Admin::user()->id;
        return Admin::form(Homework::class, function (Form $form) use($teacher_id){


            $form->text('coursename','学科名');
            $form->multipleSelect('departid','所属年级专业')->options(
                Departs::all()->pluck('project','id')
            )->load('/api/project','id','project');
            $form->editor('content','作业内容');
            $form->text('subtime','截止时间');
            $form->hidden('teacherid')->value($teacher_id);
            $form->setAction('/admin/homework/complete');
        });
    }

    public function complete(Request $request)
    {
        $data = $request->all();
        $result = Homework::create([
            'coursename' => $data['coursename'],
            'departid' => json_encode($data['departid']),
            'content' => $data['content'],
            'subtime' => $data['subtime'],
            'teacherid' => $data['teacherid']
        ]);
        if($result) {
            echo "发布作业成功";
        } else {
            echo "发布作业失败";
        }
    }
}
