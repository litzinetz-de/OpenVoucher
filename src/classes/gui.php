<?php
class admingui
{
	private $v;
	
	function __construct()
	{
		$this->v = new vouchermanager(); // An own instance of vouchermanager is needed to query the devices that are connected to each voucher
	}
	public function ListVouchers($dataset)
	{
		echo '<center><table width="80%" border="0" cellspacing="0">
		<tr class="tableheader">
		<td><b>Voucher ID</b></td>
		<td><b>Device count</b></td>
		<td><b>Valid until</b></td>
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
			<td>'.$dataset[$i]['dev_count'].'</td>
			<td>'.date('Y-m-d H:i',$dataset[$i]['valid_until']).'</td>
			<td>'.$dataset[$i]['comment'].'&nbsp;</td>
			<td>';
			
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
