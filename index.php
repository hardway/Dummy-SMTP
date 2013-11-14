<?php
	define("MAIL_DIR", __DIR__."/mails");
?>

<?php
	if(!isset($_GET['file'])){
		foreach(scandir(MAIL_DIR) as $f){
			if(preg_match('/\.eml$/', $f)){
				$date=date("Y-m-d H:i:s", filectime(MAIL_DIR."/".$f));
				echo "<li><a href='?file=$f'>$f</a> <span style='color: #999'>$date</span></li>";
			}
		}
	}
	else{
		ob_end_clean();

		$content=file_get_contents(MAIL_DIR.'/'.$_GET['file']);
		list($header, $body)=explode("\n\n", $content, 2);

		echo "<pre><code>$header</code></pre><hr/>";
		echo $body;
	}
?>
