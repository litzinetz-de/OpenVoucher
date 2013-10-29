<?php
class admingui
{
	public function ListVouchers($dataset)
	{
		echo '<center><table width="80%" border="0">
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
			<td>'.$dataset[$i]['comment'].'</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>';
			//print_r($row);
		}
		echo '</table></center>';
	}
}
?>