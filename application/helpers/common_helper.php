<?PHP
session_start();
if((!isset($_SESSION['poadmin']['uid'])) || ($_SESSION['poadmin']['uid']==""))
{
	header("location: ../index.php?msg=³¬Ê±ÁË");
}
else
{
	$name = $_SESSION['poadmin']['name'];
	$ip = $_SESSION['poadmin']['ip'];
	$last = $_SESSION['poadmin']['lastlogin'];
	$lip = $_SESSION['poadmin']['lip'];
	$llast = $_SESSION['poadmin']['llastlogin'];
}


	//-----------------------------------------------------------
	//formatchinese($str) add a space into each Chinese character
	//-----------------------------------------------------------
	function formatchinese($str){
		if(preg_match_all("/[\x{4e00}-\x{9fa5}]{1}/u",$str,$out)){
			foreach($out[0] as $value){
				$str = preg_replace("/".$value."/",$value." ",$str);
			}
		}
		return trim(preg_replace("/\s+/"," ",$str));
	}


	//-----------------------------------------------------------------
	//restorechinese($str) remove the space after each Chinse character
	//-----------------------------------------------------------------
	function restorechinese($str){
		if(preg_match_all("/[\x{4e00}-\x{9fa5}]{1} /u",$str,$out)){
			foreach($out[0] as $value){
				$str = preg_replace("/$value/",trim($value),$str);
			}
		}
		return trim($str);
	}

?>

