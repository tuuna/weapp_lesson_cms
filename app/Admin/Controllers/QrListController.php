<?php
/**
 * Created by PhpStorm.
 * User: laijie
 * Date: 2018/4/2
 * Time: 下午5:47
 *                              _ooOoo_
 *                             o8888888o
 *                             88" . "88
 *                             (| -_- |)
 *                             O\  =  /O
 *                          ____/`---'\____
 *                        .'  \\|     |//  `.
 *                       /  \\|||  :  |||//  \
 *                      /  _||||| -:- |||||-  \
 *                      |   | \\\  -  /// |   |
 *                      | \_|  ''\---/''  |   |
 *                      \  .-\__  `-`  ___/-. /
 *                    ___`. .'  /--.--\  `. . __
 *                 ."" '<  `.___\_<|>_/___.'  >'"".
 *                | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *                \  \ `-.   \_ __\ /__ _/   .-` /  /
 *           ======`-.____`-.___\_____/___.-`____.-'======
 *                              `=---='
 *           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *                      佛祖保佑        永无BUG
 *             佛曰:
 *                    写字楼里写字间，写字间里程序员；
 *                    程序人员写程序，又拿程序换酒钱。
 *                    酒醒只在网上坐，酒醉还来网下眠；
 *                    酒醉酒醒日复日，网上网下年复年。
 *                    但愿老死电脑间，不愿鞠躬老板前；
 *                    奔驰宝马贵者趣，公交自行程序员。
 *                    别人笑我忒疯癫，我笑自己命太贱；
 *                    不见满街漂亮妹，哪个归得程序员？
 */

namespace App\Admin\Controllers;

use App\Departs;
use App\Homework;
use App\Http\Controllers\Controller;
use App\Sign;
use App\Stulist;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;

class QrListController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('签到情况');
            $content->description('学生签到情况统计');
            $content->body($this->grid());
        });
    }

    /**
     * @return \Encore\Admin\Grid
     */
    protected function grid()
    {
        $teacher_id = Admin::user()->id;
        return Admin::grid(Sign::class, function (Grid $grid) use($teacher_id){
            $grid->model()->where('teacherid',$teacher_id);
            $grid->model()->orderBy('stuid');
            $grid->column('stuid','学号');
            $grid->column('name','姓名');
            $grid->column('coursename','学科名');
            $grid->column('date','时间');
            $grid->status('签到?')->display(function ($status) {
                return $status == '1' ?
                    "<i class='fa fa-check' style='color:green'></i>" :
                    "<i class='fa fa-close' style='color:red'></i>";
            });
            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->between('date', '签到时间')->datetime();
                $filter->equal('coursename','课程名')->placeholder('请输入课程名');

            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableCreateButton();
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
            'content' => strip_tags($data['content']),
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
