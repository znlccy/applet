<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/6
 * Time: 11:32
 * Comment: 获取主播控制器
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Anchor extends Controller {

    /**
     * 获取前十的主播数量
     */
    public function getLast($page) {
        $pagenumber = request()->param('pagenumber');
        if (!isset($pagenumber) || empty($pagenumber)) {
            $anchor = Db::table('dede_uploads')
            ->alias('a')
            ->group('a.arcid')
            ->distinct(true)
            ->field('a.arcid as aid')
            ->field('a.title as title,a.url as url,a.playtime as playtime, a.uptime as uptime, b.body')
            ->where('title', 'like', '%华星艺人%')
            ->whereOr('title', 'like', '%华星网红%')
            ->whereOr('title', 'like', '%华星主播%')
            ->join('dede_addonarticle b', 'a.arcid = b.aid')
            ->paginate(10, false, ['query' => $page]);
        } else {
            $anchor = Db::table('dede_uploads')
                ->alias('a')
                ->group('a.arcid')
                ->distinct(true)
                ->field('a.arcid as aid')
                ->field('a.title as title,a.url as url,a.playtime as playtime, a.uptime as uptime, b.body')
                ->where('title', 'like', '%华星艺人%')
                ->whereOr('title', 'like', '%华星网红%')
                ->whereOr('title', 'like', '%华星主播%')
                ->join('dede_addonarticle b', 'a.arcid = b.aid')
                ->paginate($pagenumber, false, ['query' => $page]);
        }



        //遍历数组
        /*foreach ($data as $key => $value) {
            $anchor[$key]['aid'] = $value['aid'];
            $anchor[$key]['title'] = $value['title'];
            $anchor[$key]['url'] = $value['url'];
            $anchor[$key]['playtime'] = $value['playtime'];
            $anchor[$key]['uptime'] = $value['uptime'];
            $anchor['description'] = strip_tags($value['body'],"<img>");
            return json($anchor);
        }*/

        /*遍历对象数组*/
        /*for ($i = 0; $i <10; $i++) {
            $result = $data[$i];
            $aid = $result['aid'];
            $title = $result['title'];
            $url = $result['url'];
            $playtime = $result['playtime'];
            $uptime = $result['uptime'];
            $body = strip_tags($result['body'], '<img>');
            return json_encode($result);
        }*/

        $data = json_encode($anchor);

        return $data;
    }

    /**
     * 根据id获取主播信息
     * @param $aid
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDetail($aid) {;
        $data = Db::table('dede_addonarticle')
            ->where('aid', $aid)
            ->field('aid, body, userip')
            ->find();

        if ($data != null) {
            //去除html标签
            $body_data = strip_tags($data['body'], '<img>');
            //重新赋值
            $secod_data = preg_replace("/\r\n/", '',$body_data);
            $third_data = preg_replace("/\t/", ' ', $secod_data);
            $final_data = str_replace("&nbsp;", '', $third_data);
            $data['body'] = $final_data;

            //返回数据
            return json($data);
        } else {
            return json(['code' => '400', 'message' => '没有您要查找的数据']);
        }
    }
}
