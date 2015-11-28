<?php
class admingui
{
	private $v;
	
	function __construct()
	{
		$this->v = new vouchermanager(); // An own instance of vouchermanager is needed to query the devices that are connected to each voucher
	}
	
	private function secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);
	$days=(int)$days;

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
	$hours=(int)$hours;

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
	$minutes=(int)$minutes;

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
	$seconds=(int)$seconds;

    $msg='';
	
	if($days>0)
	{
		$msg=$days.' days';
	}
	if($hours>0)
	{
		if(trim($msg)!='')
		{
			$msg=$msg.', ';
		}
		$msg=$msg.$hours.' hours';
	}
	if($minutes>0)
	{
		if(trim($msg)!='')
		{
			$msg=$msg.', ';
		}
		$msg=$msg.$minutes.' minutes';
	}
	if($seconds>0)
	{
		if(trim($msg)!='')
		{
			$msg=$msg.', ';
		}
		$msg=$msg.$seconds.' seconds';
	}
	return $msg;
}
	
	public function ListVouchers($dataset)
	{
		echo '<center><table width="100%" border="0" cellspacing="0">
		<tr class="tableheader">
		<td><b>Voucher ID</b></td>
		<td><b>Verification key</b></td>
		<td><b>Device count</b></td>
		<td><b>Valid until</b></td>
		<td><b>Valid for</b></td>
		<td><b>Comment</b></td>
		<td><b>Devices</b></td>
		<td><b>Drop voucher</b></td>
		</tr>';
		
		$a=true;
		for($i=0;$i<count($dataset);$i++)
		{
			if($a)
			{
				$bgclass='lightbg';
				$a=false;
			} else {
				$bgclass='darkbg';
				$a=true;
			}
			echo '<tr class="'.$bgclass.'">
			<td>'.$dataset[$i]['voucher_id'].'</td>
			<td>'.$dataset[$i]['verification_key'].'</td>
			<td>'.$dataset[$i]['dev_count'].'</td>';
			
			if($dataset[$i]['valid_until']!=0)
			{
				echo '<td>'.date('Y-m-d H:i',$dataset[$i]['valid_until']).'</td>';
			} else {
				echo '<td><div class="disabled">Not activated yet</div></td>';
			}
			
			if($dataset[$i]['valid_for']!=0)
			{
				if($dataset[$i]['valid_until']!=0) // has the voucher been activated?
				{
					echo '<td><div class="disabled">'.$this->secondsToTime($dataset[$i]['valid_for']).'</div></td>'; // if it has, grey out the "valid_until" field
				} else {
					echo '<td>'.$this->secondsToTime($dataset[$i]['valid_for']).'</td>';
				}
			} else {
				echo '<td>&nbsp;</td>';
			}
			echo '<td>'.$dataset[$i]['comment'].'&nbsp;</td><td>';
			
			$deviceinfo=$this->v->GetDeviceList($dataset[$i]['voucher_id']);
			for($j=0;$j<count($deviceinfo);$j++)
			{
				echo '['.$deviceinfo[$j]['type'].'] '.$deviceinfo[$j]['addr'].' - <a href="dropdevice.php?type='.$deviceinfo[$j]['type'].'&addr='.$deviceinfo[$j]['addr'].'">Drop</a><br>';
			}
			
			echo '&nbsp;</td>
			<td><a href="dropvoucher.php?vid='.$dataset[$i]['voucher_id'].'">Drop voucher</a></td>
			</tr>';
		}
		echo '</table></center>';
	}

	public function ListUsers($dataset)
	{
		echo '<center><table width="80%" border="0" cellspacing="0">
		<tr class="tableheader">
		<td><b>Username</b></td>
		<td><b>Permissions</b></td>
		<td><b>Delete user</b></td>
		</tr>';
		
		$a=true;
		for($i=0;$i<count($dataset);$i++)
		{
			if($a)
			{
				$bgclass='lightbg';
				$a=false;
			} else {
				$bgclass='darkbg';
				$a=true;
			}
			echo '<tr class="'.$bgclass.'">
			<td>'.$dataset[$i]['username'].'</td>
			<td>'.$dataset[$i]['permission_list'].' &gt; <a href="users.php?do=edit_perm&user='.$dataset[$i]['username'].'">Edit permissions</a></td>
			<td><a href="users.php?do=del&user='.$dataset[$i]['username'].'">Delete user</a></td>
			</tr>';
		}
		echo '</table></center>';
	}
}
?>
