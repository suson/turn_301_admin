<?php

class Common {

    private static $ci;

    public function __construct() {
        date_default_timezone_set('Asia/Shanghai');
        session_start();
        self::$ci = &get_instance();
        self::$ci->load->helper('url');
    }

    public function common() {
        if ((!isset($_SESSION['poadmin']['uid'])) || ($_SESSION['poadmin']['uid'] == "")) {
            redirect('?msg=超时了/');
        } else {
            $data['uid'] = $_SESSION['poadmin']['uid'];
            $data['roleid'] = $_SESSION['poadmin']['role'];
            $data['role'] = $this->get_role_name($_SESSION['poadmin']['role']);
            $data['name'] = $_SESSION['poadmin']['name'];
            $data['currentip'] = $_SESSION['poadmin']['currentip'];
            $data['currentlogintime'] = $_SESSION['poadmin']['currentlogintime'];
            $data['lastloginip'] = $_SESSION['poadmin']['lastloginip'];
            $data['lastlogintime'] = $_SESSION['poadmin']['lastlogintime'];
            $data['currenttag'] = 'updatepass';
        }
        return $data;
    }

    public function get_role_name($v=0){

        $arr = array(
            0 => '普通用户',
            1 => '高级用户',
            2 => '超级管理员'
            );
        return isset($arr[$v]) ? $arr[$v] : $arr[0];
    }


    public function get_status_type($v='Normal'){
        $arr = array(
            'Normal' => 1101, //正常
            'Delete' => 1001, //离线
            'Lock' => 1011 //停止
            );
        return isset($arr[$v]) ? $arr[$v] : $arr['Normal'];
    }

   //取套餐对应单价
   public function get_meal_price($v=100){
       $arr = array(
           100 => '0.2',
		   1 => '0.0005'
       );
       return isset($arr[$v]) ? $arr[$v] : $arr[100];
   }
   /**
     * 截取字符串
     * @param string $string 要截取的字符串
     * @param int $length 截取的长度
     * @param string $dot 截取后显示的字符
     */
   public static function cutstr($string, $length, $dot = '...') {

        if (strlen($string) <= $length) {
            return $string;
        }


        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

        $strcut = '';
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        return $strcut . $dot;
    }
    
    public static function get_overage(){
        $sql = "select overage from users where id=? limit 1";
        $arr = array($_SESSION['poadmin']['uid']);
        $query = self::$ci->db->query($sql, $arr);
        $row = $query->row_array();
        $overage = isset($row['overage']) ? $row['overage'] : 0;
        return $overage;
    }
    
    public static function get_username($id){
        $sql = "select username from users where id=? limit 1";
        $arr = array($id);
        $query = self::$ci->db->query($sql, $arr);
        $row = $query->row_array();
        $username = isset($row['username']) ? $row['username'] : "&nbsp;";
        return $username;
    }


}

?>