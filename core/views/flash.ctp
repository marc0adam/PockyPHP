<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= $pageTitle; ?></title>
<script type="text/javascript">
setTimeout(function() {
	document.location.assign("<?= $redirect; ?>");
}, <?= intval($delay) * 1000; ?>);
</script>
</head>
<body>
<div style="text-align:center; padding:20px 10%;">
	<h1><?= $msg; ?></h1>
	<p><a href="<?= $redirect; ?>">continue</a></p>
</div>
</body>
</html>
