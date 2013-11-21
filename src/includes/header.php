<?php
if(!class_exists('systemmanager'))
{
	require('../classes/systemmanager.php');
	$s = new systemmanager();
}
$logo=$s->GetSetting('logo');
if(file_exists('../graphics/'.$logo) && !is_dir('../graphics/'.$logo))
{
	$img_inc='../graphics/'.$logo;
} else {
	$img_inc='../graphics/logo-small.png';
}
?>
<html>
<head>
<link rel="stylesheet" href="../style/style.css">
<title>OpenVoucher</title>
</head>
<body>
<table width="100%" border="0" cellspacing="0">
<tr class="tableheader">
<td colspan="3">&nbsp;</td>
</tr><tr>
<td><img src="<?php echo $img_inc; ?>"></td>
<td align="center">
<div class="middle">OpenVoucher</div>
</td>
<td align="right"><img src="<?php echo $img_inc; ?>"></td>
</tr>
<tr class="tableheader">
<td colspan="3">&nbsp;</td>
</tr>
</table>
<br>