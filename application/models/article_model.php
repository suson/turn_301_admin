<?php

	/**
	* 
	*/
	class Article_model extends Ci_Model
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}

		function add_model(){

			$data->title = $_POST['title'];
			$data->pattern = $_POST['pattern'];
			$data->description = $_POST['description'];
			$data->pic = $_POST['pic'];
			$data->url = $_POST['url'];
			$this->db->insert('article',$data);

		}

		function get_model(){

			$query = $this->db->query("select * from article");
			foreach($query->result_array() as $v){
				//$data[] = $v['title'];

			}
			//return $data; 简单查询
			//$where['id'] = 4089;
			//$this->db->delete('article',$where); //删除
			$query = $this->db->get('article');
			foreach($query->result_array() as $v){
				$data[] = $v['title'];

			}
			//return $data; //取表里所有数据
			$ids = array(4090,4091);
			$this->db->where_in('id',$ids);
			$this->db->delete('article');
		}
	}

?>