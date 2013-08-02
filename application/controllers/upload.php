<?php

class Upload extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('common');
        $this->data = $this->common->common();
    }

    function index() {
		$this->data['error'] = '';
        $this->load->view('upload', $this->data);
    }


	function auto_import($filename){
			$file = './public/'.$filename;
            $sql = "select max(urlno) as max from urls";
            $query = $this->db->query($sql);
            $max = 10000;
            if ($res = $query->row_array()) {
                $max = $res['max'] > 10000 ? $res['max'] : 10000;
            }
            $insertdata = array();
            $f = fopen($file, "r");
            $i = 0;
			while (!feof($f)) {
					$i++;
					$line = fgets($f);
					$linedata_arr = explode(' ', trim($line));
					if (count($linedata_arr) > 2) {
						$linedata['urlname'] = isset($linedata_arr[0]) ? $linedata_arr[0] : ''; //网站名称
						$linedata['urlname'] = @iconv('GBK', 'UTF-8//IGNORE', $linedata['urlname']);
						$linedata['urlpath'] = isset($linedata_arr[1]) ? $linedata_arr[1] : ''; //网址
						$linedata['frompath'] = isset($linedata_arr[2]) ? $linedata_arr[2] : ''; //来源网址
						$linedata['meal'] = isset($linedata_arr[3]) ? $linedata_arr[3] : 100; //套餐数量
						$days = isset($linedata_arr[4]) ? intval($linedata_arr[4]) : 1; //开通天数
						$linedata['urlno'] = $max + $i; //网站编号
						$linedata['starttime'] = date("Y-m-d H:i:s");
						$linedata['endtime'] = date("Y-m-d H:i:s", strtotime("+" . $days . "days"));
						$linedata['unittimes'] = floor(intval($linedata['meal']) / 24);
						$linedata['createtime'] = date("Y-m-d H:i:s");
						$linedata['updatetime'] = date("Y-m-d H:i:s");
						$linedata['status'] = 1101;
						$linedata['scale'] = 100;
						$linedata['uid'] = $this->data['uid'];
						$insertdata[] = $linedata;
					}
				}
				if (count($insertdata) > 0)
                $this->db->insert_batch('urls', $insertdata);
            fclose($f);
            echo "<script>parent.location.href='" . base_url('admin/url') . "'</script>";
	}

    function do_upload() {
        $config['upload_path'] = './public/upload/';
        $config['allowed_types'] = 'txt';
        $config['max_size'] = '500';
        $config['max_width'] = '1024';
        $config['max_height'] = '768';
        $config['file_name'] = time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
            echo "<script>parent.alert('".$this->upload->display_errors()."')</script>";
            //$this->load->view('upload', $error);
        } else {

            $data = array('upload_data' => $this->upload->data());
            $sql = "select max(urlno) as max from urls";
            $query = $this->db->query($sql);
            $max = 10000;
            if ($res = $query->row_array()) {
                $max = $res['max'] > 10000 ? $res['max'] : 10000;
            }
            $insertdata = array();
            $f = fopen($data['upload_data']['full_path'], "r");
            $i = 0;
			if($this->data['roleid'] != 2){
				while (!feof($f)) {
					$i++;
					$line = fgets($f);
					$linedata_arr = explode(' ', trim($line));
					if (count($linedata_arr) > 2) {
						$linedata['urlname'] = isset($linedata_arr[0]) ? $linedata_arr[0] : ''; //网站名称
						$linedata['urlname'] = @iconv('GBK', 'UTF-8//IGNORE', $linedata['urlname']);
						$linedata['urlpath'] = isset($linedata_arr[1]) ? $linedata_arr[1] : ''; //网址
						//$linedata['meal'] = isset($linedata_arr[1]) ? $linedata_arr[1] : 100; //套餐数量
						//$days = isset($linedata_arr[2]) ? intval($linedata_arr[2]) : 1; //套餐数量
						$linedata['frompath'] = isset($linedata_arr[2]) ? $linedata_arr[2] : ''; //来源网址
						$linedata['urlno'] = $max + $i; //网站编号
						//$linedata['starttime'] = date("Y-m-d H:i:s");
						//$linedata['endtime'] = date("Y-m-d H:i:s", strtotime("+" . $days . "days"));
						//$linedata['unittimes'] = floor(intval($linedata['meal']) / 24);
						$linedata['createtime'] = date("Y-m-d H:i:s");
						$linedata['updatetime'] = date("Y-m-d H:i:s");
						$linedata['status'] = 1001;
						$linedata['scale'] = 100;
						$linedata['uid'] = $this->data['uid'];
						$insertdata[] = $linedata;
					}
				}
				
			}else{
				while (!feof($f)) {
					$i++;
					$line = fgets($f);
					$linedata_arr = explode(' ', trim($line));
					if (count($linedata_arr) > 2) {
						$linedata['urlname'] = isset($linedata_arr[0]) ? $linedata_arr[0] : ''; //网站名称
						$linedata['urlname'] = @iconv('GBK', 'UTF-8//IGNORE', $linedata['urlname']);
						$linedata['urlpath'] = isset($linedata_arr[1]) ? $linedata_arr[1] : ''; //网址
						$linedata['frompath'] = isset($linedata_arr[2]) ? $linedata_arr[2] : ''; //来源网址
						$linedata['meal'] = isset($linedata_arr[3]) ? $linedata_arr[3] : 100; //套餐数量
						$days = isset($linedata_arr[4]) ? intval($linedata_arr[4]) : 1; //开通天数
						$linedata['urlno'] = $max + $i; //网站编号
						$linedata['starttime'] = date("Y-m-d H:i:s");
						$linedata['endtime'] = date("Y-m-d H:i:s", strtotime("+" . $days . "days"));
						$linedata['unittimes'] = floor(intval($linedata['meal']) / 24);
						$linedata['createtime'] = date("Y-m-d H:i:s");
						$linedata['updatetime'] = date("Y-m-d H:i:s");
						$linedata['status'] = 1101;
						$linedata['scale'] = 100;
						$linedata['uid'] = $this->data['uid'];
						$insertdata[] = $linedata;
					}
				}
			}
            
            if (count($insertdata) > 0)
                $this->db->insert_batch('urls', $insertdata);
            fclose($f);
            echo "<script>parent.location.href='" . base_url('admin/url') . "'</script>";
        }
    }

}

?>