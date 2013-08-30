<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/28
 * Time: 上午 11:21
 * To change this template use File | Settings | File Templates.
 */
/**
 * 拆解Html Tag
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Parser extends Controller
{
    /**
     * index 頁
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        echo '<h2>Parser 範例</h2><br>';
        $items = array(
            Html::anchor('parser/test_35', 'test_35'),
            Html::anchor('parser/test_36', 'test_36'),
            Html::anchor('parser/test_39', 'test_39'),
            Html::anchor('parser/test_40', 'test_40'),
            Html::anchor('parser/get_test_40', 'test_40 from redis'),
            Html::anchor('parser/test_23_06', 'test_23_06'),
            Html::anchor('parser/test_23_17', 'test_23_17'),
            Html::anchor('parser/test_23_57', 'test_23_57'),
        );
        echo Html::ul($items);
    }



    /**
     * parser test_35.html
     * @access  public
     * @return  Response
     */
    public function action_test_35()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/35.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        $homepage = str_replace("&nbsp;", "", $homepage);

        // 先處理<tr>,<td>保留它
        $homepage = str_replace("<tr", "<#tr", $homepage);
        $homepage = str_replace("</tr", "<#/tr", $homepage);
        $homepage = str_replace("<td", "<#td", $homepage);
        $homepage = str_replace("</td", "<#/td", $homepage);

        // 先處理保留它
        // <span class="league-name">德国甲组联赛</span>
        // <#span class="league-name">德国甲组联赛<#/span>
        $homepage = preg_replace("/<(span[^<>]+league\-name[^<>]+>.*?<)(\/span>)/", "<#$1#$2", $homepage);

        // 剔除所有不保留的tag, 並取代為空格，合併多空格為一個空格
        // 預先整理乾淨<td></td>的內容, 避免於迴圈中進行
        $homepage = preg_replace("/<[^#][^<>]+>/", " ", $homepage);
        $homepage = trim(preg_replace("/\s+/", " ", $homepage));


        //echo $homepage;


        /**
         * Regex 處理，找<tr..>..</tr>的字串
         */
        preg_match_all("/<#tr([^<>]*?)>(.*?)<#\/tr>/m", $homepage, $matches);


        echo '<pre>';
        echo "\n\n";
        //print_r($matches);

        $group_name = null;
        $row_ident = null;
        $tmp_game_data = null;
        $data = null;
        $set_index = 0;
        $tmp_var = null;

        //echo "拆解完tr\n";
        //print_r($matches[0]);

        /**
         *  存成Array
         */
        foreach ($matches[2] as $tr_key => $tr) {

            // 取得比賽名稱
            if (strpos($tr,"league-name")) {
                //<span class="league-name">德国甲组联赛</span>
                preg_match("/<#span[^<>]+league\-name[^<>]+>(.*?)<#\/span>/", $tr, $match_group);
                $group_name = $match_group[1];
            }

            /**
             * Regex 處理，找<td..>..</td>之中的字串
             */
            preg_match_all("/<#td([^<>]*?)> *(.*?) *<#\/td>/m", $tr, $match_td);

            // 拆出識別字，0,1,2
            preg_match("/row-nonlive-\d+-(\d+)/m", $matches[1][$tr_key], $match_ident);
            if (isset($match_ident[1])) {
                $row_ident = $match_ident[1];
            } else {
                $row_ident = -1;
            }
            //echo "row_ident: $row_ident\n";
            if ($row_ident == 0 && $match_td[2][1] != '' ) {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 同一賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }

                //echo "set each ele";
                $set_index = 0;
                $tmp_game_data = $match_td[2];
                for($i=2;$i<9;$i++) {
                    $tmp_var = $tmp_game_data[$i];
                    $tmp_game_data[$i] = null;
                    $tmp_game_data[$i][$set_index] = $tmp_var;
                }

                $tmp_game_data['group'] = $group_name;
            } else if ($row_ident == 0) {
                $set_index++;
                for ($i=0;$i<10;$i++) {
                    if ($match_td[2][$i] != '') {
                        $tmp_game_data[$i][$set_index] = $match_td[2][$i];
                    }
                }
            } else if ($row_ident == 1 || $row_ident == 2) {
                for ($i=0;$i<7;$i++) {
                    if ( $match_td[2][$i] != '') {
                        $tmp_game_data[$i+2][$set_index] .= '#'.$match_td[2][$i];
                    }
                }
            } else {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 進入新賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }
            }

            //print_r($match_td[2]);
            //print_r($tmp_game_data);
        }

        if (!is_null($tmp_game_data)) {
            // 寫入game_data, 進入新賽別時的寫入
            $data[] = $tmp_game_data;
            $tmp_game_data = null;
        }

        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        $sep_str = '  ';
        foreach ($data as $game) {
            $tmp = null;

            $teams = explode(" ",$game[1]);
            $tmp = array(
                'LEAGUE_NAME' => $game['group'],
                'GDATE' => $game[0],
                'TEAM_NAME_H' => $teams[0],
                'TEAM_NAME_C' => $teams[1],
            );

            $tmp['A_ASIA'] = $game[2];
            $tmp['A_BS'] = $game[3];
            $tmp['A_OT'] = $game[4];
            $tmp['A_OE'] = $game[5];

            $tmp['U_ASIA'] = $game[6];
            $tmp['U_BS'] = $game[7];
            $tmp['U_OT'] = $game[8];
            $new_data[] = $tmp;
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        echo "\n\n";
        echo '</pre>';
    }

    /**
     * parser test_36.html
     * @access  public
     * @return  Response
     */
    public function action_test_36()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/36.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        $homepage = str_replace("&nbsp;", "", $homepage);

        // 先處理<tr>,<td>保留它
        $homepage = str_replace("<tr", "<#tr", $homepage);
        $homepage = str_replace("</tr", "<#/tr", $homepage);
        $homepage = str_replace("<td", "<#td", $homepage);
        $homepage = str_replace("</td", "<#/td", $homepage);

        // 先處理保留它
        // <span class="league-name">德国甲组联赛</span>
        // <#span class="league-name">德国甲组联赛<#/span>
        $homepage = preg_replace("/<(span[^<>]+league\-name[^<>]+>.*?<)(\/span>)/", "<#$1#$2", $homepage);

        // 剔除所有不保留的tag, 並取代為空格，合併多空格為一個空格
        // 預先整理乾淨<td></td>的內容, 避免於迴圈中進行
        $homepage = preg_replace("/<[^#][^<>]+>/", " ", $homepage);
        $homepage = trim(preg_replace("/\s+/", " ", $homepage));

        //echo $homepage;
        /**
         * Regex 處理，找<tr..>..</tr>的字串
         */
        preg_match_all("/<#tr([^<>]*?)>(.*?)<#\/tr>/m", $homepage, $matches);


        echo '<pre>';
        echo "\n\n";
        //print_r($matches);

        $group_name = null;
        $row_ident = null;
        $tmp_game_data = null;
        $data = null;
        $set_index = 0;
        $tmp_var = null;
        //print_r($matches[0]);

        /**
         *  存成Array
         */
        foreach ($matches[2] as $tr_key => $tr) {

            // 取得比賽名稱
            if (strpos($tr,"league-name")) {
                //<span class="league-name">德国甲组联赛</span>
                preg_match("/<#span[^<>]+league\-name[^<>]+>(.*?)<#\/span>/", $tr, $match_group);
                $group_name = $match_group[1];
            }

            /**
             * Regex 處理，找<td..>..</td>之中的字串
             */
            preg_match_all("/<#td([^<>]*?)> *(.*?) *<#\/td>/m", $tr, $match_td);

            // 拆出識別字，0,1,2
            preg_match("/row-nonlive-\d+-(\d+)/m", $matches[1][$tr_key], $match_ident);
            if (isset($match_ident[1])) {
                $row_ident = $match_ident[1];
            } else {
                $row_ident = -1;
            }
            //echo "row_ident: $row_ident\n";

            if ($row_ident == 0 && $match_td[2][1] != '' ) {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 同一賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }

                //echo "set each ele";
                $set_index = 0;
                $tmp_game_data = $match_td[2];
                for($i=2;$i<9;$i++) {
                    $tmp_var = $tmp_game_data[$i];
                    $tmp_game_data[$i] = null;
                    $tmp_game_data[$i][$set_index] = $tmp_var;
                }

                $tmp_game_data['group'] = $group_name;
            } else if ($row_ident == 0) {
                $set_index++;
                for ($i=0;$i<10;$i++) {
                    if ($match_td[2][$i] != '') {
                        $tmp_game_data[$i][$set_index] = $match_td[2][$i];
                    }
                }
            } else if ($row_ident == 1 || $row_ident == 2) {
                for ($i=0;$i<7;$i++) {
                    if ( $match_td[2][$i] != '') {
                        $tmp_game_data[$i+2][$set_index] .= '#'.$match_td[2][$i];
                    }
                }
            } else {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 進入新賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }
            }
            //print_r($tmp_game_data);
        }

        if (!is_null($tmp_game_data)) {
            // 寫入game_data, 進入新賽別時的寫入
            $data[] = $tmp_game_data;
            $tmp_game_data = null;
        }

        //print_r($data);

        /**
         *  設成更具體的結構
         */
        $new_data = null;
        $sep_str = '  ';
        foreach ($data as $game) {
            $tmp = null;

            $teams = explode(" ",$game[1]);
            $tmp = array(
                'LEAGUE_NAME' => $game['group'],
                'GDATE' => $game[0],
                'TEAM_NAME_H' => $teams[0],
                'TEAM_NAME_C' => $teams[1],
            );

            $tmp['A_ASIA'] = $game[2];
            $tmp['A_BS'] = $game[3];
            $tmp['A_OT'] = $game[4];
            $tmp['A_OE'] = $game[5];

            $tmp['U_ASIA'] = $game[6];
            $tmp['U_BS'] = $game[7];
            $tmp['U_OT'] = $game[8];
            $new_data[] = $tmp;
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        echo "\n\n";
        echo '</pre>';
    }

    /**
     * parser test_39.html
     * @access  public
     * @return  Response
     */
    public function action_test_39()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/39.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));
        //echo $homepage;

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        /**
         * Regex 處理，找<span..>..</span>的字串
         */
        preg_match_all("/<span(.*?)>([^<>]*?)<\/span>/m", $homepage, $matches);


        /**
         * Regex 處理，處理時間 區塊
         */
        preg_match_all("/<div.*?\"time\-column-content\">(.*?)<\/div>/m", $homepage, $matches_2);


        echo '<h2>波胆</h2>';
        echo '<pre>';
        echo "\n\n";
        //print_r($matches);
        //print_r($matches_2[1]);

        /**
         *  存成Array
         */

        $size = count($matches[1]);
        $item = 0;
        $ele_count = 0;
        $time_index = 0;
        $tmp = array();

        for ($i=0; $i<$size; $i++) {
            if (strpos($matches[1][$i],"league-name")) {
                $item++;
                $data[$item]['group'] = $matches[2][$i];
                //$data[$item]['time'] = str_replace("<br>"," ",trim($matches_2[1][$item-1]));
            } else if ($item>0) {
                $tmp[] = $matches[2][$i];
                $ele_count++;
                if ($ele_count == 28) {
                    array_unshift($tmp, str_replace("<br>"," ",trim($matches_2[1][$time_index++])));
                    $data[$item]['data'][] = $tmp;
                    $tmp = array();
                    $ele_count = 0;
                }
                //$data[$item]['data'][] = $matches[2][$i];
            } else {
                //$data[$item]['data'][] = $matches[2][$i];
            }
        }

        /**
         * 塞值前印出
         */
        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        foreach ($data as $g_key => $group) {
            foreach ($group['data'] as $game) {
                $tmp = null;
                $tmp = array(
                    'LEAGUE_NAME' => $group['group'],
                    'GDATE' => $game[0],
                    'TEAM_NAME_H' => $game[1],
                    'TEAM_NAME_C' => $game[2],

                    '1:0_H' => $game[3],
                    '2:0_H' => $game[4],
                    '2:1_H' => $game[5],
                    '3:0_H' => $game[6],
                    '3:1_H' => $game[7],
                    '3:2_H' => $game[8],
                    '4:0_H' => $game[9],
                    '4:1_H' => $game[10],
                    '4:2_H' => $game[11],
                    '4:3_H' => $game[12],

                    '1:0_C' => $game[19],
                    '2:0_C' => $game[20],
                    '2:1_C' => $game[21],
                    '3:0_C' => $game[22],
                    '3:1_C' => $game[23],
                    '3:2_C' => $game[24],
                    '4:0_C' => $game[25],
                    '4:1_C' => $game[26],
                    '4:2_C' => $game[27],
                    '4:3_C' => $game[28],

                    '0:0' => $game[13],
                    '1:1' => $game[14],
                    '2:2' => $game[15],
                    '3:3' => $game[16],
                    '4:4' => $game[17],
                    'other' => $game[18]
                );
                $new_data[] = $tmp;
            }
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);
        echo "\n\n";
        echo '</pre>';
    }

    /**
     * parser test_40.html
     * @access  public
     * @return  Response
     */
    public function action_test_40()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/40.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));
        //echo $homepage;

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        /**
         * Regex 處理，找<span..>..</span>的字串
         */
        preg_match_all("/<tbody.*?>(.*?)<\/tbody>/m", $homepage, $matches);


        echo '<h2>半场/全场</h2>';
        echo '<pre>';
        echo "\n\n";
        //print_r($matches[1]);

        /**
         *  存成Array
         */

        $i=-1;
        foreach ($matches[1] as $row) {
            preg_match_all("/>([^<>\s]+?)</m", $row, $matches_2);
            //print_r($matches_2[1]);
            if (count($matches_2[1]) == 1) {
                $i++;
                $data[$i]['group'] = $matches_2[1][0];
            } else if (count($matches_2[1]) == 13) {
                $data[$i]['data'][] = $matches_2[1];
            }
        }


        /**
         * 塞值前印出
         */
        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        foreach ($data as $g_key => $group) {
            foreach ($group['data'] as $game) {
                $tmp = null;
                $tmp = array(
                    'LEAGUE_NAME' => $group['group'],
                    'GDATE' => $game[0].' '.$game[1],
                    'TEAM_NAME_H' => $game[2],
                    'TEAM_NAME_C' => $game[3],

                    'HH' => $game[4],
                    'HP' => $game[5],
                    'HC' => $game[6],
                    'PH' => $game[7],
                    'PP' => $game[8],
                    'PC' => $game[9],
                    'CH' => $game[10],
                    'CP' => $game[11],
                    'CC' => $game[12],
                );
                $new_data[] = $tmp;
            }
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        // 建立 Redis 'mystore' 實例
        $redis = \Fuel\Core\Redis::forge('default');
        foreach ($new_data as $row) {
            $redis->rpush('game_redis', json_encode($row));
        }


        echo "\n\n";
        echo '</pre>';

    }

    /**
     * parser test_40.html
     * @access  public
     * @return  Response
     */
    public function action_get_test_40()
    {
        // 建立 Redis 'mystore' 實例
        $redis = \Fuel\Core\Redis::forge('default');

        echo '<h2>半场/全场</h2>';
        echo '<pre>';
        echo "\n\n";
        $length = $redis->llen('game_redis');
        echo 'Array 數量:'.$length."\n";
        for ($i=0;$i<$length;$i++) {
            print_r(json_decode($redis->LINDEX('game_redis',$i)));
        }
        echo "\n\n";
        echo '</pre>';

    }
    /**
     * parser test_23_06.html
     * @access  public
     * @return  Response
     */
    public function action_test_23_06()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/23_06.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        $homepage = str_replace("&nbsp;", "", $homepage);

        // 先處理<tr>,<td>保留它
        $homepage = str_replace("<tr", "<#tr", $homepage);
        $homepage = str_replace("</tr", "<#/tr", $homepage);
        $homepage = str_replace("<td", "<#td", $homepage);
        $homepage = str_replace("</td", "<#/td", $homepage);

        // 先處理保留它
        // <span class="league-name">德国甲组联赛</span>
        // <#span class="league-name">德国甲组联赛<#/span>
        $homepage = preg_replace("/<(span[^<>]+league\-name[^<>]+>.*?<)(\/span>)/", "<#$1#$2", $homepage);

        // 剔除所有不保留的tag, 並取代為空格，合併多空格為一個空格
        // 預先整理乾淨<td></td>的內容, 避免於迴圈中進行
        $homepage = preg_replace("/<[^#][^<>]+>/", " ", $homepage);
        $homepage = trim(preg_replace("/\s+/", " ", $homepage));


        //echo $homepage;


        /**
         * Regex 處理，找<tr..>..</tr>的字串
         */
        preg_match_all("/<#tr([^<>]*?)>(.*?)<#\/tr>/m", $homepage, $matches);


        echo '<pre>';
        echo "\n\n";
        //print_r($matches);

        $group_name = null;
        $row_ident = null;
        $tmp_game_data = null;
        $data = null;
        $set_index = 0;
        $tmp_var = null;

        //echo "拆解完tr\n";
        //print_r($matches[0]);

        /**
         *  存成Array
         */
        foreach ($matches[2] as $tr_key => $tr) {

            // 取得比賽名稱
            if (strpos($tr,"league-name")) {
                //<span class="league-name">德国甲组联赛</span>
                preg_match("/<#span[^<>]+league\-name[^<>]+>(.*?)<#\/span>/", $tr, $match_group);
                $group_name = $match_group[1];
            }

            /**
             * Regex 處理，找<td..>..</td>之中的字串
             */
            preg_match_all("/<#td([^<>]*?)> *(.*?) *<#\/td>/m", $tr, $match_td);

            // 拆出識別字，0,1,2
            preg_match("/row-nonlive-\d+-(\d+)/m", $matches[1][$tr_key], $match_ident);
            if (isset($match_ident[1])) {
                $row_ident = $match_ident[1];
            } else {
                $row_ident = -1;
            }
            //echo "row_ident: $row_ident\n";
            if ($row_ident == 0 && $match_td[2][1] != '' ) {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 同一賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }

                //echo "set each ele";
                $set_index = 0;
                $tmp_game_data = $match_td[2];
                for($i=2;$i<9;$i++) {
                    $tmp_var = $tmp_game_data[$i];
                    $tmp_game_data[$i] = null;
                    $tmp_game_data[$i][$set_index] = $tmp_var;
                }

                $tmp_game_data['group'] = $group_name;
            } else if ($row_ident == 0) {
                $set_index++;
                for ($i=0;$i<10;$i++) {
                    if ($match_td[2][$i] != '') {
                        $tmp_game_data[$i][$set_index] = $match_td[2][$i];
                    }
                }
            } else if ($row_ident == 1 || $row_ident == 2) {
                for ($i=0;$i<7;$i++) {
                    if ( $match_td[2][$i] != '') {
                        $tmp_game_data[$i+2][$set_index] .= '#'.$match_td[2][$i];
                    }
                }
            } else {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 進入新賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }
            }

            //print_r($match_td[2]);
            //print_r($tmp_game_data);
        }

        if (!is_null($tmp_game_data)) {
            // 寫入game_data, 進入新賽別時的寫入
            $data[] = $tmp_game_data;
            $tmp_game_data = null;
        }

        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        $sep_str = '  ';
        foreach ($data as $game) {
            $tmp = null;

            $teams = explode(" ",$game[1]);
            $tmp = array(
                'LEAGUE_NAME' => $game['group'],
                'GDATE' => $game[0],
                'TEAM_NAME_H' => $teams[0],
                'TEAM_NAME_C' => $teams[1],
            );

            $tmp['A_ASIA'] = $game[2];
            $tmp['A_BS'] = $game[3];
            $tmp['A_OT'] = $game[4];
            $tmp['A_OE'] = $game[5];

            $tmp['U_ASIA'] = $game[6];
            $tmp['U_BS'] = $game[7];
            $tmp['U_OT'] = $game[8];
            $new_data[] = $tmp;
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        echo "\n\n";
        echo '</pre>';
    }

    /**
     * parser test_23_17.html
     * @access  public
     * @return  Response
     */
    public function action_test_23_17()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/23_17.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        $homepage = str_replace("&nbsp;", "", $homepage);

        // 先處理<tr>,<td>保留它
        $homepage = str_replace("<tr", "<#tr", $homepage);
        $homepage = str_replace("</tr", "<#/tr", $homepage);
        $homepage = str_replace("<td", "<#td", $homepage);
        $homepage = str_replace("</td", "<#/td", $homepage);

        // 先處理保留它
        // <span class="league-name">德国甲组联赛</span>
        // <#span class="league-name">德国甲组联赛<#/span>
        $homepage = preg_replace("/<(span[^<>]+league\-name[^<>]+>.*?<)(\/span>)/", "<#$1#$2", $homepage);

        // 剔除所有不保留的tag, 並取代為空格，合併多空格為一個空格
        // 預先整理乾淨<td></td>的內容, 避免於迴圈中進行
        $homepage = preg_replace("/<[^#][^<>]+>/", " ", $homepage);
        $homepage = trim(preg_replace("/\s+/", " ", $homepage));


        //echo $homepage;


        /**
         * Regex 處理，找<tr..>..</tr>的字串
         */
        preg_match_all("/<#tr([^<>]*?)>(.*?)<#\/tr>/m", $homepage, $matches);


        echo '<pre>';
        echo "\n\n";
        //print_r($matches);

        $group_name = null;
        $row_ident = null;
        $tmp_game_data = null;
        $data = null;
        $set_index = 0;
        $tmp_var = null;

        /**
         * 資料格式，參數設定
         *
         */
        $head_row_length=9;
        $row_length=6;
        $field_offset=2;
        $field_limit = $field_offset + $row_length;

        //echo "拆解完tr\n";
        //print_r($matches[0]);

        /**
         *  存成Array
         */
        foreach ($matches[2] as $tr_key => $tr) {

            // 取得比賽名稱
            if (strpos($tr,"league-name")) {
                //<span class="league-name">德国甲组联赛</span>
                preg_match("/<#span[^<>]+league\-name[^<>]+>(.*?)<#\/span>/", $tr, $match_group);
                $group_name = $match_group[1];
            }

            /**
             * Regex 處理，找<td..>..</td>之中的字串
             */
            preg_match_all("/<#td([^<>]*?)> *(.*?) *<#\/td>/m", $tr, $match_td);

            // 拆出識別字，0,1,2
            preg_match("/row-nonlive-\d+-(\d+)/m", $matches[1][$tr_key], $match_ident);
            if (isset($match_ident[1])) {
                $row_ident = $match_ident[1];
            } else {
                $row_ident = -1;
            }
            //echo "row_ident: $row_ident\n";
            if ($row_ident == 0 && $match_td[2][1] != '' ) {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 同一賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }

                //echo "set each ele";
                $set_index = 0;
                $tmp_game_data = $match_td[2];

                for($i=$field_offset; $i<$field_limit; $i++) {
                    $tmp_var = $tmp_game_data[$i];
                    $tmp_game_data[$i] = null;
                    $tmp_game_data[$i][$set_index] = $tmp_var;
                }

                $tmp_game_data['group'] = $group_name;
            } else if ($row_ident == 0) {
                $set_index++;
                for ($i=0;$i<$head_row_length;$i++) {
                    if ($match_td[2][$i] != '') {
                        $tmp_game_data[$i][$set_index] = $match_td[2][$i];
                    }
                }
            } else if ($row_ident == 1 || $row_ident == 2) {
                for ($i=0;$i<$row_length;$i++) {
                    if ( $match_td[2][$i] != '') {
                        $tmp_game_data[$i+2][$set_index] .= '#'.$match_td[2][$i];
                    }
                }
            } else {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 進入新賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }
            }

            //echo "match_td<br>";
            //print_r($match_td[2]);
            //print_r($tmp_game_data);
        }

        if (!is_null($tmp_game_data)) {
            // 寫入game_data, 進入新賽別時的寫入
            $data[] = $tmp_game_data;
            $tmp_game_data = null;
        }

        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        $sep_str = '  ';
        foreach ($data as $game) {
            $tmp = null;

            $teams = explode(" ",$game[1]);
            $tmp = array(
                'LEAGUE_NAME' => $game['group'],
                'GDATE' => $game[0],
                'TEAM_NAME_H' => $teams[0],
                'TEAM_NAME_C' => $teams[1],
            );

            $tmp['A_ASIA'] = $game[2];
            $tmp['A_BS'] = $game[3];
            $tmp['A_OE'] = $game[4];

            $tmp['U_ASIA'] = $game[5];
            $tmp['U_BS'] = $game[6];
            $tmp['U_OT'] = $game[7];
            $new_data[] = $tmp;
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        echo "\n\n";
        echo '</pre>';
    }

    /**
     * parser test_23_57.html
     * @access  public
     * @return  Response
     */
    public function action_test_23_57()
    {
        $homepage = file_get_contents('http://localhost/parser/rawdata/23_57.html');

        $startstr = '<div class="c-column">';
        $endstr ='<div class="service-link-block">';
        $pos_start = strpos($homepage, $startstr);
        $pos_end = strpos($homepage, $endstr);
        $homepage = substr($homepage,$pos_start+strlen($startstr),$pos_end-$pos_start-strlen($startstr));

        /**
         * 測試用 的 部分資料
         *
         */
        //$str = '';
        //$homepage = $str;


        $homepage = str_replace("&nbsp;", "", $homepage);

        // 先處理<tr>,<td>保留它
        $homepage = str_replace("<tr", "<#tr", $homepage);
        $homepage = str_replace("</tr", "<#/tr", $homepage);
        $homepage = str_replace("<td", "<#td", $homepage);
        $homepage = str_replace("</td", "<#/td", $homepage);

        // 先處理保留它
        // <span class="league-name">德国甲组联赛</span>
        // <#span class="league-name">德国甲组联赛<#/span>
        $homepage = preg_replace("/<(span[^<>]+league\-name[^<>]+>.*?<)(\/span>)/", "<#$1#$2", $homepage);

        // 區分 table
        //<div id="odds-display-live" class="odds-block live">
        //<div id="odds-display-nonlive" class="odds-block">
        $homepage = preg_replace("/<div[^<>]+(odds-display-\w*live)[^<>]+>/", "<#tr><#td>$1<#/td><#/tr>", $homepage);

        // 剔除所有不保留的tag, 並取代為空格，合併多空格為一個空格
        // 預先整理乾淨<td></td>的內容, 避免於迴圈中進行
        $homepage = preg_replace("/<[^#][^<>]+>/", " ", $homepage);
        $homepage = trim(preg_replace("/\s+/", " ", $homepage));


        //echo $homepage;


        /**
         * Regex 處理，找<tr..>..</tr>的字串
         */
        preg_match_all("/<#tr([^<>]*?)>(.*?)<#\/tr>/m", $homepage, $matches);


        echo '<pre>';
        echo "\n\n";
        //print_r($matches[2]);



        $data = null;           // 儲存所有比賽資料
        $tmp_game_data = null;  // 暫存個別比賽資料 - 判斷邏輯中使用

        $group_name = null;     // 儲存賽制名稱 - 判斷邏輯中使用
        $table_name = null;     // 儲存table A or B - 判斷邏輯中使用

        $row_ident = null;      // 區別tr是開始，還是中間部分 - 判斷邏輯中使用
        $set_index = 0;         // 是第一組、第二組 或 第三組資料
        $tmp_var = null;        // swap 使用的變數

        /**
         * 資料格式，參數設定 - 判斷邏輯中使用
         *
         */
        $head_row_length=10;
        $row_length=7;
        $field_offset=2;
        $field_limit = $field_offset + $row_length;

        //echo "拆解完tr\n";
        //print_r($matches[0]);

        /**
         *  存成Array
         */
        foreach ($matches[2] as $tr_key => $tr) {

            // 取得賽制名稱
            if (strpos($tr,"league-name")) {
                //<span class="league-name">德国甲组联赛</span>
                preg_match("/<#span[^<>]+league\-name[^<>]+>(.*?)<#\/span>/", $tr, $match_group);
                $group_name = $match_group[1];
            }

            // 區分table
            if (strpos($tr,"odds-display")) {
                if (strpos($tr,"odds-display-live")) {
                    $table_name = 'A';
                } else if (strpos($tr,"odds-display-nonlive")) {
                    $table_name = 'B';
                }
            }

            /**
             * Regex 處理，找<td..>..</td>之中的字串
             */
            preg_match_all("/<#td([^<>]*?)> *(.*?) *<#\/td>/m", $tr, $match_td);

            // 拆出識別字，0,1,2
            /*
            preg_match("/row-nonlive-\d+-(\d+)/m", $matches[1][$tr_key], $match_ident);
            if (isset($match_ident[1])) {
                $row_ident = $match_ident[1];
            } else {
                $row_ident = -1;
            }
            */

            $tr_count = count($match_td[2]);
            if ($tr_count==10) {
                $row_ident = 0;
            } else if ($tr_count==7) {
                $row_ident = 1;
            } else {
                $row_ident = -1;
            }

            //echo "row_ident: $row_ident\n";
            if ($row_ident == 0 && $match_td[2][1] != '' ) {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 同一賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }

                //echo "set each ele";
                $set_index = 0;
                $tmp_game_data = $match_td[2];

                for($i=$field_offset; $i<$field_limit; $i++) {
                    $tmp_var = $tmp_game_data[$i];
                    $tmp_game_data[$i] = null;
                    $tmp_game_data[$i][$set_index] = $tmp_var;
                }

                $tmp_game_data['group'] = $group_name;
                $tmp_game_data['table'] = $table_name;
            } else if ($row_ident == 0) {
                $set_index++;
                for ($i=0;$i<$head_row_length;$i++) {
                    if ($match_td[2][$i] != '') {
                        $tmp_game_data[$i][$set_index] = $match_td[2][$i];
                    }
                }
            } else if ($row_ident == 1 || $row_ident == 2) {
                for ($i=0;$i<$row_length;$i++) {
                    if ( $match_td[2][$i] != '') {
                        $tmp_game_data[$i+2][$set_index] .= '#'.$match_td[2][$i];
                    }
                }
            } else {
                if (!is_null($tmp_game_data)) {
                    // 寫入game_data, 進入新賽別時的寫入
                    $data[] = $tmp_game_data;
                    $tmp_game_data = null;
                }
            }

            //echo "match_td<br>";
            //print_r($match_td[2]);
            //print_r($tmp_game_data);
        }

        if (!is_null($tmp_game_data)) {
            // 寫入game_data, 進入新賽別時的寫入
            $data[] = $tmp_game_data;
            $tmp_game_data = null;
        }

        //print_r($data);


        /**
         *  設成更具體的結構
         */
        $new_data = null;
        $sep_str = '  ';
        foreach ($data as $game) {
            $tmp = null;

            $teams = explode(" ",$game[1]);
            $tmp = array(
                'LEAGUE_NAME' => $game['group'],
                'GDATE' => $game[0],
                'TEAM_NAME_H' => $teams[0],
                'TEAM_NAME_C' => $teams[1],
                'TABLE' => $game['table'],
            );

            if ($game['table'] == 'A') {
                $tmp['A_ASIA'] = $game[3];
                $tmp['A_BS'] = $game[4];
                $tmp['A_OT'] = $game[5];
            } else {
                $tmp['A_ASIA'] = $game[2];
                $tmp['A_BS'] = $game[3];
                $tmp['A_OT'] = $game[4];
                $tmp['A_OE'] = $game[5];
            }
            $tmp['U_ASIA'] = $game[6];
            $tmp['U_BS'] = $game[7];
            $tmp['U_OT'] = $game[8];

            $new_data[] = $tmp;
        }

        /**
         * 塞值後印出
         */
        print_r($new_data);

        echo "\n\n";
        echo '</pre>';
    }

}