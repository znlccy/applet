<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/6
 * Time: 13:15
 * Comment: 新闻控制器
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class News extends Controller {

    /**
     * 获取最新分页消息
     * @param $page
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getLast($page) {
        $pagenumber = request()->param('pagenumber');
        if (!isset($pagenumber) || empty($pagenumber)) {
            $data = Db::table('dede_archives')
                ->where('title', 'not like', '%华星%')
                ->field('id,title,shorttitle,writer,source,litpic,pubdate,senddate,keywords,description')
                ->paginate(10,false, ['query' => $page]);
        } else {
            $data = Db::table('dede_archives')
                ->where('title', 'not like', '%华星%')
                ->field('id,title,shorttitle,writer,source,litpic,pubdate,senddate,keywords,description')
                ->paginate($pagenumber,false, ['query' => $page]);
        }

        return json($data);
    }

    /**
     * 获取新闻详情
     * @param $aid
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDetail($aid) {
        $data = Db::table('dede_addonarticle')
            ->where('aid', $aid)
            ->field('aid, body, userip')
            ->find();

        if ($data != null) {
            //去掉html所有标签
            $body_data = strip_tags($data['body'],'<img>');
            $secod_data = preg_replace("/\r\n/", '',$body_data);
            $third_data = preg_replace("/\t/", ' ', $secod_data);
            $forth_data = str_replace("&ldquo", '', $third_data);
            $fifth_data = str_replace('&rdquo', '', $forth_data);
            $final_data = str_replace("&nbsp;", '', $fifth_data);

            //重新赋值
            $data['body'] = $final_data;
            return json($data);
        } else {
            return json(['code' => '400', 'message' => '没有您要查找的数据']);
        }
    }
}