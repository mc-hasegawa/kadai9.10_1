<?php
/**
* ページング処理
*/
class Pagingclass
{
	function previous_page($nowpage)
	{
		for ($i=$nowpage-3; $i <= $nowpage; $i++)	//前のページのリンク生成(最大4つまで)
		{ 
			if (1 < $i)
			{
				printf("&nbsp<a href='?next_page=%s'>%s</a>&nbsp",$i-1,$i-1);
			}
		}
	}
	function following_page($nowpage,$pagenum)
	{
		for ($i=$nowpage; $i < $pagenum; $i++)//次のページのリンク生成(最大4つまで)
		{ 
			if ($nowpage+3 < $i)
			{
				break;
			}
			printf("&nbsp;<a href='?next_page=%s'>%s</a>&nbsp;",$i+1,$i+1);
		}
	}
}

?>