<?php
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('Asia/Tokyo');
require "db.php";
require "paging.php";
$link = mysql_connect($host, $username, $pass);
$db = mysql_select_db($dbname, $link);
mysql_query('SET NAMES utf8', $link );
define("RESPONSE_SHOW_LIMIT",10);

$log_sql = "SELECT * FROM `bbs_log_table`";
$log_data_all = mysql_query($log_sql);
$res_num = mysql_num_rows($log_data_all);
$page_num = ceil($res_num/RESPONSE_SHOW_LIMIT);
$now_page = $page_num;
if (isset($_GET["next_page"]))
{
	$now_page = (int)$_GET["next_page"];
}
$start_log_num = $res_num-(RESPONSE_SHOW_LIMIT*($page_num-$now_page+1));
if ($start_log_num < 0)	//ページング処理で参照ログ数が0未満になった場合強制的に一番最初から見れるようにする
{
	$start_log_num = 0;
}
$sql = "SELECT * FROM `bbs_log_table` LIMIT $start_log_num,10";
$bbs_log_data = mysql_query($sql);

$unix_time = time();
$show_time = date('Y/m/d H:i:s',$unix_time);
$show_count = 1;
//投稿内容をテーブルに追加
if (isset($_POST["posting_content"]))
{
	$user_name = $_POST["posting_user_name"];
	if ($user_name == "")
	{
		$user_name = "匿名希望";
	}
	$content = $_POST["posting_content"];
	$user_name = htmlspecialchars($user_name);
	$content = htmlspecialchars($content);
	$insert_res_num = $res_num+1;
	$insert_sql = "INSERT INTO `bbs_log_table` VALUES ('$insert_res_num','$show_time','$user_name','$content',1)";
	if ($insert_res_num <= 1000)
	{
		if (!mysql_query($insert_sql))
		{
			die("書き込みに失敗しました");
		}
		
	}
	header("Location: ./index.php");
	exit();
}
// 削除実行時にパスワード設定が必要か？
if (isset($_GET["get_delete_id"]))
{
	$delete_res_id = $_GET["get_delete_id"];
	$delete_sql = "UPDATE  `lesson`.`bbs_log_table` SET  `res_state` = 0 WHERE  `bbs_log_table`.`res_id` ='$delete_res_id'";
	if (!mysql_query($delete_sql))
	{
		die("削除に失敗しました");
	}
	header("Location: ./index.php");
	exit();
}
?>
<html>
<head>
<title>PHP課題9.10_1</title>
</head>
<script>
function posting_check()
{
	if(document.posting_form.posting_content.value == "")
	{
		document.posting_form.posting_button.disabled = "true";
	}
	else
	{
		document.posting_form.posting_button.disabled = "";
	}
}
function res_delete_check(delete_res_id)
{
	var delete_flag = confirm("投稿番号"+delete_res_id+"を削除してもよろしいですか？");
	return delete_flag;
}
</script>
<body>
	<p>PHP課題9.10_1</p>
	<p>
	<?php
	if (1000 <= $res_num)
	{
		echo "投稿数が1000を超えました<br>これ以上は書き込むことができません";
	}
	?>
	</p>
	<p>
	<?php
	$pageing = new Pagingclass();
	printf("&nbsp<a href='?next_page=%s'><<</a>&nbsp",1);
	$pageing->previous_page($now_page);
	printf("&nbsp;%sページ&nbsp;",$now_page);
	$pageing->following_page($now_page,$page_num);
	printf("&nbsp<a href='?next_page=%s'>>></a>&nbsp",$page_num);
	?>
	</p>
	<p>
		======================================<br>
		<?php
		if ($res_num != 0)
		{
			while($row = mysql_fetch_assoc($bbs_log_data))
			{
				$show_res_id = $row[print_r("res_id",true)];
				$show_user_name = $row[print_r("user_name",true)];
				$show_res_time = $row[print_r("res_time",true)];
				$show_content = str_replace("\n","<br>",$row[print_r("content",true)]);
				echo $show_res_id.", ";
				echo $show_user_name;
				echo " (投稿日時: ".$show_res_time." )<br>";
				if ($row[print_r("res_state",true)] == 1)
				{
					echo $show_content."<br>";
				}
				else
				{
					echo "この投稿は削除されました。<br>";
				}
				printf("<br>[<a href='?get_delete_id=%s' onclick=\"return res_delete_check(%s);\">削除</a>]",$show_res_id,$show_res_id);
				if ($show_count < RESPONSE_SHOW_LIMIT)
				{
					echo "<br>--------------------------------------<br>";
				}
				$show_count++;
			}
		}
		else
		{
			echo "<br>投稿内容がありません<br>";
		}
		?>
		<br>======================================<br>
	</p>
	<p>
	<?php
	printf("&nbsp<a href='?next_page=%s'><<</a>&nbsp",1);
	$pageing->previous_page($now_page);
	printf("&nbsp;%sページ&nbsp;",$now_page);
	$pageing->following_page($now_page,$page_num);
	printf("&nbsp<a href='?next_page=%s'>>></a>&nbsp",$page_num);
	?>
	</p>
	<form action="" method="post" name="posting_form">
		名前
		<p><input type="text" name="posting_user_name" value=""></p>
		書き込む内容
		<p><textarea name="posting_content" rows="6" cols="40" wrap="hard" onChange="posting_check()"></textarea></p>
		<input name="posting_button" type="submit" value="書き込む" disabled>
	</form>
</body>
</html>