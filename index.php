<?php
	define("MAIL_DIR", __DIR__."/mails");
	define('SCRIPT_URL', 'http://'.$_SERVER['HTTP_HOST'].'/'.parse_url($_SERVER['REQUEST_URI'])['path']);

	if($_POST["action"]){
		switch($_POST['action']){
			case 'delete':
				@unlink(MAIL_DIR.'/'.$_POST['email']);
				break;
			case 'delete_all':
				break;
		}
		header("Location: ".$_SERVER['HTTP_REFERER']);
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dummy SMTP Emails</title>
	<style>
		body {font-family: Helvetica, Arial, sans-serif; font-size: 13px;}
		a {color: #390;}
		.pull-right {float:right;}
		.pull-left {float: left;}
		.clearfix {clear: both;}
		.muted {color: #CCC;}

		.dummy-sidebar {width: 460px; position: absolute; top:0; left:0; bottom:0; background: #EEE; border-right: 1px solid #CCC;}
		.dummy-content {margin-left: 460px; padding: 10px;}
		.dummy-content .meta {border-bottom: 1px solid #CCC; padding-bottom: 15px; margin-bottom: 15px;}

		.dummy-sidebar ul {margin: 0; padding: 0;}
		.dummy-sidebar li.email {list-style: none;}
		.dummy-sidebar li.email a {display: block; text-decoration: none; color: #000; padding: 5px; border-bottom: 1px solid #DDD;}
		.dummy-sidebar li.current a {background: #FFD;}
	</style>
</head>
<body>
<?php
	function parse_email($path){
		if(!file_exists($path)) return false;

		$raw=file_get_contents($path);
		$time=date("Y-m-d H:i:s", filectime($path));

		list($raw_header, $body)=explode("\n\n", $raw, 2);

		$header=array();
		foreach(explode("\n", $raw_header) as $line){
			list($key, $val)=array_map('trim', explode(":", $line, 2));
			$header[$key]=$val;
		}
		$from=$header['From'];
		$to=$header['To'];
		$subject=$header['Subject'];

		return compact('time', 'from', 'to', 'subject', 'body', 'header', 'raw');
	}

	function display_email_list($dir){
		echo "<ul>";

		$emails=array_filter(scandir(MAIL_DIR), function($f){return preg_match('/\.eml$/', $f);});
		rsort($emails);

		if(empty($emails)){
			echo "<h2 class='muted' align='center'>No emails found in <br/>".MAIL_DIR."</h2>";
		}

		foreach($emails as $f){
			$e=parse_email(MAIL_DIR."/".$f);

			$class="email".($f==$_GET['email'] ? ' current' : '');

			echo "
				<li class='$class'><a href='".SCRIPT_URL."?email=$f'>
					{$e['subject']}<br/>
					<span style='color: #0058ba;'>
						{$e['to']}
						<span class='muted'>{$e['time']}</span>
					</span>
				</a></li>
			";
		}
		echo "</ul>";
	}

	function display_email_content($email=null){
		if(empty($email)) return;

		$e=parse_email(MAIL_DIR."/".$email);
		if(empty($e)) return;

		echo "
			<div class='meta'>
				<div class='pull-right'>
					To: {$e['to']}, {$e['time']}
				</div>
				<div>{$e['subject']}</div>
				<div class='clearfix'></div>

				<div class='pull-right'>
					<form style='display:inline-block;' method='post'>
						<input type='hidden' name='action' value='delete' />
						<input type='hidden' name='email' value='$email' />
						<input type='submit' value='Delete' />
					</form>
				</div>
				<div class='clearfix'></div>
			</div>
		";
		echo "<div>{$e['body']}</div>";
	}

	echo "<div><div class='dummy-sidebar'>";
	display_email_list();
	echo "</div><div class='dummy-content'>";
	display_email_content($_GET['email']);
	echo "</div></div>";
?>
</body>
</html>
