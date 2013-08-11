<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    private $json = array('success' => false, 'message' => 'success', 'data' => array());

    public function __construct() {
        date_default_timezone_set('Asia/Shanghai');
        parent::__construct();
    }

    public function index() {
        //$this->load->database();
        if ((isset($_POST['username'])) && (isset($_POST['password']))) {
            $remember = isset($_POST['reme']) ? $_POST['reme'] : 'off';
            $uname = mysql_real_escape_string(strip_tags(trim($_POST['username'])));
            $pw = mysql_real_escape_string(strip_tags(trim($_POST['password'])));
            $sql = "SELECT * FROM `users` WHERE username = '" . $uname . "' AND userpass = '" . MD5($pw) . "'";
            $query = $this->db->query($sql);
            $msg = "";

            if ($query->num_rows() > 0) {
                if ($remember == "on") {
                    setcookie("loginuser", urlencode($uname), time() + 60 * 60 * 24 * 30, "/");  //30天有效期
                }
                session_start();
                $row = $query->row();
                $_SESSION['poadmin']['uid'] = $row->id;
                $_SESSION['poadmin']['name'] = $row->username;
                $_SESSION['poadmin']['lastloginip'] = $row->lastloginip;
                $_SESSION['poadmin']['lastlogintime'] = date('l jS \of F Y h:i:s A', strtotime($row->lastlogintime));
                $_SESSION['poadmin']['role'] = $row->role;
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $sqlupdate = "UPDATE `users` SET `lastloginip` = '$ip', `lastlogintime` = '" . date('Y-m-d H:i:s') . "' WHERE username = '" . $uname . "' limit 1";
                $this->db->query($sqlupdate);
                $_SESSION['poadmin']['currentip'] = $ip;
                $_SESSION['poadmin']['currentlogintime'] = date('l jS \of F Y h:i:s A');
            } else {
                $msg = "用户名/密码不正确";
            }
            if ($msg == "") {
                $this->json['success'] = true;
                $this->json['message'] = $msg;
                echo json_encode($this->json);
                exit;
            }
            $this->json['message'] = $msg;
            echo json_encode($this->json);
            exit;
        } elseif (isset($_GET['msg'])) {
            $msg = urldecode($_GET['msg']);
        } else {
            $msg = "";
        }
        $data['msg'] = $msg;
        $data['current'] = 'login';
        $data['loginuser'] = isset($_COOKIE['loginuser']) ? $_COOKIE['loginuser'] : '';
        $data['checked'] = isset($_COOKIE['loginuser']) ? 'checked' : '';
        $this->load->view('home', $data);
    }

    public function reg() {
        if (isset($_POST['username']) && ($_POST['username'] !== "") && isset($_POST['password']) && ($_POST['password'] !== "") && isset($_POST['ppassword']) && ($_POST['ppassword'] !== "")) {
            $this->load->database();
            $uname = mysql_real_escape_string(strip_tags(trim($_POST['username'])));
            $pw = mysql_real_escape_string(strip_tags(trim($_POST['password'])));
            $pww = mysql_real_escape_string(strip_tags(trim($_POST['ppassword'])));
            $c = preg_match("/^[a-zA-Z]+[0-9]+$/", $uname);
            if ($c) {
                if ($pw == $pww) {
                    $sql = "SELECT * FROM `users` WHERE username = '" . $uname . "'";
                    $query = $this->db->query($sql);
                    $msg = "";
                    if ($query->num_rows() == 0) {
                        $pw = MD5($pw);
                        $sql = "INSERT INTO `users` (`id`,`username`,`userpass`,`regtime`,`updatetime`) VALUES (NULL,?,?,?,?)";
                        $data = array($uname, $pw, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
                        $query = $this->db->query($sql, $data);
                        if ($this->db->affected_rows()) {
                            $this->json['success'] = true;
                            $this->json['message'] = "注册成功，请登陆！";
                            echo json_encode($this->json);
                            exit;
                        } else {
                            $msg = "注册失败!";
                        }
                    } else {
                        $msg = "该用户名已存在！";
                    }
                } else {
                    $msg = "两次密码不一致！";
                }//两次密码尖刺
            } else {
                $msg = "用户名为字母与数字组合,不能带有特殊字符!";
            }//用户名纯英文检测
            $this->json['message'] = $msg;
            echo json_encode($this->json);
            exit;
        } elseif (isset($_GET['msg'])) {
            $msg = htmlentities($_GET['msg']);
            //$msg =$_GET['msg'];
        } else {
            $msg = "";
        }
        $data['current'] = 'reg';
        $data['checked'] = isset($_COOKIE['loginuser']) ? 'checked' : '';
        $this->load->view('home', $data);
    }

    public function logout() {
        session_start();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
        $siteurl = base_url('?msg=You have logout');
        redirect($siteurl);
    }

    public function turn_301() {
		
		if((date('H:i')<"06:00" || date('H:i') > "22:00")){
		
			echo json_encode($this->json);
			exit;
		}
		
        //$sql = "SELECT * FROM `urls` where status=1101 and UNIX_TIMESTAMP(endtime)>UNIX_TIMESTAMP(now()) and (UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(firsttime)>3600 or (UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(firsttime)<3600 and unittimes-nowunittimes>0)) order by rand() limit 1";
		$sql = "SELECT * FROM `turn_url` order by rand() limit 1";
        $query = $this->db->query($sql);
        $res = $query->row_array();
        if ($res != null) {
            $this->update_url_data($res);
        }else{
			$sql = "truncate table turn_url ";
			$this->db->query($sql);
			$sql = "INSERT INTO `turn_url` (id,frompath,firsttime,turntime) SELECT id,frompath,firsttime,turntime from `urls` where status=1101 and UNIX_TIMESTAMP(endtime)>UNIX_TIMESTAMP(now()) and (UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(firsttime)>3600 or (UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(firsttime)<3600 and unittimes-nowunittimes>0)) order by rand() limit 0,2000";
			$query = $this->db->query($sql);
			$sql = "SELECT * FROM `turn_url` order by rand() limit 1";
			$query = $this->db->query($sql);
			$res = $query->row_array();
			if ($res != null) {
				$this->update_url_data($res);
			}
		}
        
        echo json_encode($this->json);
    }
    

	private function update_url_data($res){
			$id = $res['id'];
			$sql = "delete from turn_url where id =?";
			$this->db->query($sql, array($id));
            $c = (time() - strtotime($res['firsttime'])) / (60 * 60); //一小时
            $todaytimes = 1;
            if (date('Y-m-d', strtotime($res['turntime'])) == date('Y-m-d'))
                $todaytimes = 'todaytimes+1';
            if ($c > 1) {

                $sql = "update urls set nowunittimes = 1,todaytimes =$todaytimes,sumtimes = sumtimes+1,firsttime=?,turntime=? where id=? limit 1";
                $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id));
                $this->json['success'] = true;
                $this->json['data'] = $res['frompath'];
                $this->json['message'] = '跳转成功';
            } else {
                $sql = "update urls set nowunittimes = nowunittimes+1,todaytimes = $todaytimes,sumtimes = sumtimes+1,turntime=? where id=? limit 1";
                $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $id));
                $this->json['success'] = true;
                $this->json['data'] = $res['frompath'];
                $this->json['message'] = '跳转成功';
            }
	}


	public function admin(){
        $this->load->view('admin');
    }

}

?>