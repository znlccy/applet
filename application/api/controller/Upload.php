<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/9
 * Time: 11:39
 * Comment: 上传接口控制器
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Upload extends Controller {

    /**
     * 返回的数据
     * @var
     */
    private $data;

    /**
     * 保存联系人
     */
    public function save() {

        $name = $this->request->param('name');
        $nickname = $this->request->param('nickname');
        $weixin = $this->request->param('weixin');
        $phone = $this->request->request('phone');
        $intent = $this->request->param('intent');
        $create_time = time();                                  //获得当前时间戳
        /*$now_date = date('Y-m-d H:i:s', $create_time);*/  //把时间转化为当前日期

        $insert_data = ['name' => $name, 'nickname' => $nickname, 'weixin' => $weixin, 'phone' => $phone, 'intent' => $intent,'update_time' => 0, 'create_time' => $create_time];
        $config = Db::connect('db_config');
        $result = $config->table('zb_web_apply')
            ->insertGetId($insert_data);

        /*$result = $config->execute('insert into zb_web_apply (name,nickname,weixin,phone,intent,update_time,create_time) values (?,?,?,?,?,?,?)',[$name,$nickname,$weixin,$phone,$intent,0,$create_time]);*/
        if ($result >= 0) {
            $data = ['code' => '200', 'message' => '保存成功!', 'id' => $result];
            return json($data);
        } else {
            $data = ['code' => '400', 'message' => '保存失败'];
            return json($data);
        }
    }

    /**
     * 文件上传
     */
    public function upload() {
        //获取表单上传文件
        $image = request()->file('image');
        $title = request()->param('title');
        $content = request()->param('content');
        $update_time = 0;
        $create_time = time();

        //移动到框架应用根目录/public/uploads目录下
        $info = $image->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            //成功上传后，获取上传信息
            //输出jpg
            /*echo '文件扩展名:' . $info->getExtension() .'<br>';*/
            //输出文件格式
            /*echo '文件详细的路径加文件名:' . $info->getSaveName() .'<br>';*/
            //输出文件名称
            /*echo '文件保存的名:' . $info->getFilename();*/
            $sub_path = str_replace('\\', '/', $info->getSaveName());
            $image_path =  'public/' . 'uploads/' . $sub_path;
            $insert_data = ['title' => $title, 'image' => $image_path, 'content' => $content, 'update_time' => 0, 'create_time' => $create_time];
            $config = Db::connect('db_config');
            $id = $config->table('zb_web_complain')
                ->insertGetId($insert_data);

            if ($id >=0 ) {
                return json(['code' => '200', 'message' => '投诉成功!', 'id' => $id]);
            }
        } else {
            //上传失败获取错误信息
            return json(['code' => '400', 'message' => '投诉失败!' . $image->getError()]);
            /*echo $image->getError();*/
        }
    }
}