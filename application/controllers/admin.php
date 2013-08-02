<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {

    private $data;
    private $json = array('success' => false, 'message' => '', 'data' => array());

    public function __construct() {
        parent::__construct();
        $this->load->library('Common');
        $this->data = $this->common->common();
    }

    public function index() {
		$sql = "update urls set status=?,meal=? where endtime<?";
		$arr = array(1001,0,date('Y-m-d H:i:s'));
		$this->db->query($sql,$arr);
        if ($this->data['roleid'] == 0) {
            $this->updatepass();
        } else {
            $this->url();
        }
    }

    public function url() {

        if ($this->data['roleid'] == 0) {
            $this->index();
            return;
        }
        $action = $this->uri->segment(3, '');
        $id = $this->uri->segment(4, '');
        if (isset($action) && $action == 'add') {
            $urlpath = isset($_POST['urlpath']) ? $_POST['urlpath'] : '';
            $urlname = isset($_POST['urlname']) ? $_POST['urlname'] : '';
            $fromon = isset($_POST['from_on']) ? $_POST['from_on'] : '';
            $scale = 0;
            $frompath = '';
            if ($fromon == 'on') {
                $frompath = isset($_POST['frompath']) ? $_POST['frompath'] : '';
                $scale = isset($_POST['fromscale']) ? intval($_POST['fromscale']) : 100;
            }

            $id = isset($_POST['urlid']) ? $_POST['urlid'] : 0;
            if (intval($id) > 0) { //修改
                $sql = "update urls set urlpath=?,urlname=?,updatetime =?,frompath=?,scale=? where id=?";
                $query = $this->db->query($sql, array($urlpath, $urlname, date('Y-m-d H:i:s'), $frompath, $scale, $id));
                if ($query) {
                    $this->json['success'] = true;
                    $this->json['message'] = '修改成功!';
                } else {
                    $this->json['message'] = '修改失败！';
                }
            } else {
                $sql = "select max(urlno) as sum from urls";
                $query = $this->db->query($sql);
                $row = $query->row_array();
                $sum = $row['sum'] == null ? 10000 : intval($row['sum']) + 1;
                $sql = "insert into urls(uid,urlno,urlpath,urlname,createtime,updatetime,frompath,scale) values(?,?,?,?,?,?,?,?)";
                $query = $this->db->query($sql, array($this->data['uid'], $sum, $urlpath, $urlname, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $frompath, $scale));
                if ($query) {
                    $this->json['success'] = true;
                    $this->json['message'] = '添加成功!';
                } else {
                    $this->json['message'] = '添加失败！';
                }
            }
            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && $action == 'open') {
            $meal = isset($_POST['meal']) ? intval($_POST['meal']) : 100;
            $servertimes = isset($_POST['servertimes']) ? intval($_POST['servertimes']) : 1;
            $id = isset($_POST['urlid']) ? $_POST['urlid'] : 0;
            $sql = "select * from urls where find_in_set(id,?) and meal>0 and meal != ?";
            $arr = array($id, $meal);
            $query = $this->db->query($sql, $arr);
            if ($query->row_array()) {
                $this->json['message'] = '选中的套餐与您当前开通的套餐不一致！';
                echo json_encode($this->json);
                exit;
                exit;
            }
            $sql = "select overage from users where id=? limit 1";
            $arr = array($this->data['uid']);
            $query = $this->db->query($sql, $arr);
            $row = $query->row_array();
            if ($row) {
                $price = ($this->common->get_meal_price(1) * $servertimes * $meal / 1) * count(explode(',', $id));
                $overage = $row['overage'];
                if ($overage < $price && $this->data['roleid'] != 2) {
                    $this->json['message'] = '对不起，您当前账户余额不足，请联系管理员充值！';
                    echo json_encode($this->json);
                    exit;
                }
                $sql = "update urls set meal=?,starttime=if(starttime='0000-00-00 00:00:00',?,if(status='1001',now(),starttime)),endtime=if(endtime='0000-00-00 00:00:00',?,if(status='1001',?,DATE_ADD(endtime,INTERVAL $servertimes DAY))),updatetime=?,status=?,unittimes=? where find_in_set(id,?) and uid=?";
                $arr = array($meal, date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('+' . $servertimes . 'days')),date('Y-m-d H:i:s', strtotime('+' . $servertimes . 'days')), date('Y-m-d H:i:s'),1101, floor(intval($meal) / 24), $id, $this->data['uid']);
                $query = $this->db->query($sql, $arr);
                if ($query) {
                    if ($this->data['roleid'] != 2) { //非超级管理员时更新余额
                        $sql = "update users set overage=?,updatetime=? where id=?";
                        $arr = array($overage - $price, date('Y-m-d H:i:s'), $this->data['uid']);
                        $this->db->query($sql, $arr);
                    }
                    $this->json['success'] = true;
                    $this->json['message'] = '开通成功!';
                } else {
                    $this->json['message'] = '开通失败！';
                }
            }

            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && ($action == 'stopshare' || $action == 'startshare')) {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $status = $action === 'stopshare' ? 1011 : 1101;
            $oldstatus = $action !== 'stopshare' ? 1011 : 1101;
            $sql = "update urls set status=?,updatetime=? where id=? and status=? limit 1";
            if (strstr($id, ',')) {
                $sql = "update urls set status=?,updatetime=? where find_in_set(id,?) and status=?";
            }
            $arr = array($status, date('Y-m-d H:i:s'), $id, $oldstatus);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '操作成功!';
            } else {
                $this->json['message'] = '操作失败！';
            }
            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && $action == 'del') {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $sql = "delete from urls where find_in_set(id,?) and (status=? or UNIX_TIMESTAMP(endtime)<UNIX_TIMESTAMP(now()))";
            $arr = array($id, 1001);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '操作成功!';
            } else {
                $this->json['message'] = '操作失败！';
            }
            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && $action == 'close') {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $sql = "select meal,group_concat(id) as ids,group_concat(endtime) as endtimes from urls where find_in_set(id,?) and status!=1001 and meal>0 and uid=? and (UNIX_TIMESTAMP(endtime)-UNIX_TIMESTAMP(now())>(3600*24)) group by meal";
            $arr = array($id, $this->data['uid']);
            $query = $this->db->query($sql, $arr);
            $row = $query->result_array();
            if (!$row) {
                $this->json['message'] = "您选择的网址不可退订！";
                echo json_encode($this->json);
                exit;
            }
            if (count($row) > 1) {
                $this->json['message'] = "请选择相同套餐的网址！";
                echo json_encode($this->json);
                exit;
            }
            $overage = 0;
            foreach (explode(',', $row[0]['endtimes']) as $v) {
                if (ceil((strtotime($v) - time()) / (24 * 60 * 60)) > 1) {
                    //剩余天数*每100IP一天0.2元*总IP/100 * 个数
                    $overage += floor((strtotime($v) - time()) / (24 * 60 * 60)) * $this->common->get_meal_price(1) * ($row[0]['meal'] / 1);
                }
            }
            $sql = "update urls set endtime=concat(date_add(date(now()),INTERVAL 1 day),' ',DATE_FORMAT(endtime,'%H:%i:%s')),updatetime=? where find_in_set(id,?)";
            $arr = array(date('Y-m-d H:i:s'), $row[0]['ids']);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                //退还所剩余额
                $sql = "update users set overage=overage+$overage where id=" . $this->data['uid'];
                $this->db->query($sql);
                $this->json['success'] = true;
                $this->json['message'] = '操作成功!';
            } else {
                $this->json['message'] = '操作失败！';
            }
            echo json_encode($this->json);
            exit;
        } else {
            $content = $this->url_list();
        }
        $this->data['content'] = $content;
        $this->data['currenttag'] = 'url';
        $this->data['currenttagname'] = '网址管理';
        $this->load->view('index', $this->data);
    }

    public function url_list() {
        $search = isset($_POST['search']) ? mysql_real_escape_string($_POST['search']) : '';
        $page = intval($this->uri->segment(4, 1));
        $uri_segment = 4; //url第几个参数代表页码
        if ($this->uri->segment(4) === 'page' && $this->uri->segment(3) !== '') {
            $search = $this->uri->segment(3);
            $search = urldecode($search);
            $page = intval($this->uri->segment(5, 1));
            $uri_segment = 5;
        }
        $perNumber = 50; //一页显示条数
        $startCount = ($page - 1) * $perNumber;
        $resql = "SELECT [param] FROM `urls` where [where] [order] [limit]";
        $sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
        $replace_arr = array("count(*) as count", "`uid` = ? ", '', '');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("count(*) as count", "`uid` = ? or 1 ", '', '');
        }
        $countsql = str_replace($sarch_arr, $replace_arr, $resql);
        $count_where = array($this->data['uid']);
        $replace_arr = array("*", "`uid` = ?", 'order by status desc', 'limit  ?,?');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("*", "`uid` = ? or 1", 'order by status desc', 'limit  ?,?');
        }
        $sql = str_replace($sarch_arr, $replace_arr, $resql);
        $list_where = array($this->data['uid'], $startCount, $perNumber);
        if ($search !== '') {
            $sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
            $replace_arr = array("count(*) as count", "`uid` =? and (`urlname` like ? or `urlpath` like ?)", '', '');
            if ($this->data['roleid'] == 2) { //超级管理员
                $replace_arr = array("count(*) as count", "(`uid` =? or 1) and (`urlname` like ? or `urlpath` like ?)", '', '');
            }
            $countsql = str_replace($sarch_arr, $replace_arr, $resql);
            $count_where = array($this->data['uid'], "%$search%", "%$search%");
            $replace_arr = array("*", "`uid` =? and (`urlname` like ? or `urlpath` like ?)", 'order by status desc', 'limit  ?,?');
            if ($this->data['roleid'] == 2) { //超级管理员
                $replace_arr = array("*", "(`uid` =? or 1) and (`urlname` like ? or `urlpath` like ?)", 'order by status desc', 'limit  ?,?');
            }
            $sql = str_replace($sarch_arr, $replace_arr, $resql);
            $list_where = array($this->data['uid'], "%$search%", "%$search%", $startCount, $perNumber);
        }
        $query = $this->db->query($countsql, $count_where);
        $count = $query->row()->count;
        $query = $this->db->query($sql, $list_where);
        //$str = $this->db->last_query();
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/url/') . "/$search/page";
        $config['total_rows'] = $count;
        $config['per_page'] = $perNumber;
        $config['uri_segment'] = $uri_segment;
        $config['num_links'] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['cur_tag_open'] = "<a href=\"javascript:void(0);\"><strong>";
        $config['cur_tag_close'] = "</strong></a>";
        $this->pagination->initialize($config);
        $this->data['list'] = $query->result_array();
        $this->data['count'] = $count;
        $replace_arr = array("count(*) as count", "`uid` = ? and status=? ", '', '');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("count(*) as count", "(`uid` = ? or 1) and status=? ", '', '');
        }
        $countsql = str_replace($sarch_arr, $replace_arr, $resql);
        $count_where = array($this->data['uid'], 1101);
        $query = $this->db->query($countsql, $count_where);
        $currentcount = $query->row()->count;
        $replace_arr = array("count(*) as count", "`uid` = ? and status!=? ", '', '');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("count(*) as count", "(`uid` = ? or 1) and status!=? ", '', '');
        }
        $countsql = str_replace($sarch_arr, $replace_arr, $resql);
        $count_where = array($this->data['uid'], 1001);
        $query = $this->db->query($countsql, $count_where);
        $todaycount = $query->row()->count;
        $this->data['currentcount'] = $currentcount;
        $this->data['todaycount'] = $todaycount;
        $this->data['totalPage'] = ceil($count / $perNumber);
        $this->data['page'] = $this->pagination->create_links();
		//今日/累计分享
		$sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
        $replace_arr = array("sum(todaytimes) as sumtoday,sum(sumtimes) as alltimes", "`uid` = ?", '', '');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("sum(todaytimes) as sumtoday,sum(sumtimes) as alltimes", "(`uid` = ? or 1)", '', '');
        }
		$sql = str_replace($sarch_arr, $replace_arr, $resql);
		$list_where = array($this->data['uid']);
		$query = $this->db->query($sql, $list_where);
		$this->data['today_all_sum'] = $query->row()->sumtoday.'/'.$query->row()->alltimes;
		//今日开通套餐
		$sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
        $replace_arr = array("sum(meal) as todaymeal", "`uid` = ? and status !=? and date_format(updatetime,'%Y-%m-%d')=?", '', '');
        if ($this->data['roleid'] == 2) { //超级管理员
            $replace_arr = array("sum(meal) as todaymeal", "(`uid` = ? or 1) and status !=? and date_format(updatetime,'%Y-%m-%d')=?", '', '');
        }
		$sql = str_replace($sarch_arr, $replace_arr, $resql);
		$list_where = array($this->data['uid'],1001,date('Y-m-d'));
		$query = $this->db->query($sql, $list_where);
		$this->data['todayip'] = $query->row()->todaymeal ? $query->row()->todaymeal : 0;
 
        return $this->load->view('url_list', $this->data, true);
    }

    public function updatepass() {
        if (isset($_POST['oldpass']) && $_POST['oldpass'] != '' && isset($_POST['newpass']) && $_POST['newpass'] && isset($_POST['newppass']) && $_POST['newppass'] != '') {
            $id = $this->data['uid'];
            $oldpass = mysql_real_escape_string(strip_tags(trim($_POST['oldpass'])));
            $newpass = mysql_real_escape_string(strip_tags(trim($_POST['newpass'])));
            $newppass = mysql_real_escape_string(strip_tags(trim($_POST['newppass'])));
            $sql = "select id from users where id = ? and userpass = ?";
            $query = $this->db->query($sql, array($id, md5($oldpass)));
            if ($query->num_rows() < 1) {
                $this->json['message'] = '旧密码不正确！';
                echo json_encode($this->json);
                exit;
            }
            $sql = "update users set userpass = ?,updatetime = ? where id = ? and userpass = ? ";
            $query = $this->db->query($sql, array(md5($newpass), date('Y-m-d H:i:s'), $id, md5($oldpass)));
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '修改密码成功，请记住新密码';
            } else {
                $this->json['message'] = '修改密码失败';
            }
            echo json_encode($this->json);
            exit;
        }
        $this->data['content'] = $this->load->view('updateuser', $this->data, true);
        $this->data['currenttag'] = 'updatepass';
        $this->data['currenttagname'] = '修改密码';
        $this->load->view('index', $this->data);
    }

    public function users() {
        if ($this->data['roleid'] != 2) {
            $this->index();
            return;
        }
        $action = $this->uri->segment(3, '');
        $id = $this->uri->segment(4, '');
        if (isset($action) && $action == 'setrole') {
            $role = isset($_POST['role']) ? intval($_POST['role']) : 0;
            $userid = isset($_POST['id']) ? $_POST['id'] : '';
            $oldrole = $role === 0 ? 1 : 0;
            $sql = "update users set role=?,updatetime=? where id=? and role=? limit 1";
            if (strstr($userid, ',')) {
                $sql = "update users set role=?,updatetime=? where find_in_set(id,?) and role=?";
            }
            $arr = array($role, date('Y-m-d H:i:s'), $userid, $oldrole);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '操作成功!';
            } else {
                $this->json['message'] = '操作失败！';
            }
            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && $action == 'setoverage') {
            if ($this->data['roleid'] != 2) {
                $this->json['message'] = '您没有此权限！';
                echo json_encode($this->json);
                exit;
            }
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $price = isset($_POST['price']) ? $_POST['price'] : 0;
            $sql = "update users set overage = overage+$price where find_in_set(id,?) and role=1 ";
            $arr = array($id);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '充值成功!';
            } else {
                $this->json['message'] = '充值失败！';
            }
            echo json_encode($this->json);
            exit;
        } elseif (isset($action) && $action == 'resetpass') {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $sql = "update users set userpass =? where find_in_set(id,?) and role!=2 ";
            $arr = array(md5(123456), $id);
            $query = $this->db->query($sql, $arr);
            if ($query) {
                $this->json['success'] = true;
                $this->json['message'] = '密码已重置为123456';
            } else {
                $this->json['message'] = '重置密码失败！';
            }
            echo json_encode($this->json);
            exit;
        } else {
            $content = $this->user_list();
        }
        $this->data['content'] = $content;
        $this->data['currenttag'] = 'users';
        $this->data['currenttagname'] = '用户管理';
        $this->load->view('index', $this->data);
    }

    public function user_list() {
        $search = isset($_POST['search']) ? mysql_real_escape_string($_POST['search']) : '';
        $page = intval($this->uri->segment(4, 1));
        //$this->data['uid'] = 0;
        $uri_segment = 4; //url第几个参数代表页码
        if ($this->uri->segment(4) === 'page' && $this->uri->segment(3) !== '') {
            $search = $this->uri->segment(3);
            $search = urldecode($search);
            $page = intval($this->uri->segment(5, 1));
            $uri_segment = 5;
        }
        $perNumber = 50; //一页显示条数
        $startCount = ($page - 1) * $perNumber;
        $resql = "SELECT [param] FROM `users` where [where] [order] [limit]";
        $sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
        $replace_arr = array("count(*) as count", "1=1", '', '');
        $countsql = str_replace($sarch_arr, $replace_arr, $resql);
        $count_where = array();
        $replace_arr = array("*", "1=1", 'order by updatetime desc', 'limit  ?,?');
        $sql = str_replace($sarch_arr, $replace_arr, $resql);
        $list_where = array($startCount, $perNumber);
        if ($search !== '') {
            $sarch_arr = array("[param]", "[where]", "[order]", "[limit]");
            $replace_arr = array("count(*) as count", "`username` like ?", '', '');
            $countsql = str_replace($sarch_arr, $replace_arr, $resql);
            $count_where = array("%$search%");
            $replace_arr = array("*", "`username` like ?", 'order by updatetime desc', 'limit  ?,?');
            $sql = str_replace($sarch_arr, $replace_arr, $resql);
            $list_where = array("%$search%", $startCount, $perNumber);
        }
        $query = $this->db->query($countsql, $count_where);
        $count = $query->row()->count;
        $query = $this->db->query($sql, $list_where);
        $str = $this->db->last_query();
        $this->load->library('pagination');
        $config['base_url'] = base_url('admin/users/') . "/$search/page";
        $config['total_rows'] = $count;
        $config['per_page'] = $perNumber;
        $config['uri_segment'] = $uri_segment;
        $config['num_links'] = 1;
        $config['use_page_numbers'] = TRUE;
        $config['cur_tag_open'] = "<a href=\"javascript:void(0);\"><strong>";
        $config['cur_tag_close'] = "</strong></a>";
        $this->pagination->initialize($config);
        $this->data['list'] = $query->result_array();
        $this->data['count'] = $count;
        $this->data['totalPage'] = ceil($count / $perNumber);
        $this->data['page'] = $this->pagination->create_links();
        return $this->load->view('user_list', $this->data, true);
    }

}

?>