<?php

namespace App\Admin\Controllers;
use App\Qr;
use App\Sign;
use App\Stulist;
use App\User;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class QrController extends Controller
{
    use ModelForm;
    /**
     * Index interface.
     *
     *
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('二维码生成');
            $content->description('生成签到码');
            $content->body($this->form());
        });
    }

    public function form()
    {
        $teacher_id = Admin::user()->id;
        return Admin::form(Qr::class, function (Form $form) use($teacher_id){
            $form->text('coursename','学科名')->placeholder("请输入确切的学科名");
            $form->hidden('teacherid')->value($teacher_id);
            $form->hidden('date')->value(date('Y-m-d'));
            $form->setAction('/admin/qr/complete');
        });
    }

    public function qr(Request $request)
    {
        $data = $request->all();
        $secret = 'tuuna';
        $time = now('Asia/Shanghai')->format('H:i:s');
        $token = base64_encode($time.'-'.$data['coursename'].'-'.$data['teacherid'].'-'.$secret);
        $results = Stulist::where(['coursename' => $data['coursename'],'teacherid' => $data['teacherid']])->get();
        foreach ($results as $result) {
            Sign::create([
                'name' => $result['name'],
                'coursename' => $result['coursename'],
                'stuid' => $result['stuid'],
                'date' => $data['date'],
                'teacherid' => $data['teacherid']
            ]);
        }
        Qr::create([
            'coursename' => $data['coursename'],
            'date' => $data['date'],
            'teacherid' => $data['teacherid'],
        ]);
        return view('admin.qr',["token" => $token]);
    }
}