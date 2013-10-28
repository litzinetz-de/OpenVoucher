<?php
require('../classes/adminauth.php');
require('../classes/vouchermanager.php');

$a = new adminauth();
$v = new vouchermanager();

include('../includes/header.php');

include('menu.php');

echo '<table width="80%" border="0">
<tr cass="tableheader">
<td width="20%">Voucher ID</td>
<td width="20%">Valid until</td>
<td width="20%">Connected devices</td>
<td width="20%">Drop devices</td>
<td width="20%">Drop voucher</td>
</tr>';

// List all vouchers
if($a->CheckPermission('view_voucher'))
{

	// --- browse list ---
	// The following code lets the user browse the vouchers - helpful for environments with a lot of vouchers.
	// We use the variable $searchstring to pass a WHERE clause to the script - this is needed if the user wants to search for a voucher.
	// I'm sorry for the german variable names and the bad comments in the following few lines.
	// I got the code from a german developer. Hope it is readable ;)
	$pfad = $_SERVER['PHP_SELF'];    // get path for the navigation

	$query=mysql_query("SELECT COUNT(*) AS cnt FROM vouchers ".$searchstring); 
	$buffer=mysql_fetch_array($query);
	$total=$buffer['cnt'];

	// ------------------------------------------------------------------------

	$datensaetze_pro_seite = "30";      // How many vouchers shall be displayed on one page?
	$p = "3";                                // Amount of links that shall be displayed in the navigation below the list

	$seiten = ceil($total / $datensaetze_pro_seite);     // Calculates the total count of pages

	// ------------------------------------------------------------------------

	if(empty($_GET['go'])){ // if go is empty...

		$go = 1;             // set it

	}elseif($_GET['go'] <= 0 || $_GET['go'] > $seiten){

		$go = 1;

	}else{ 

		$go = mysql_real_escape_string($_GET['go']);
	}

	$links = array(); // We use an array to define all links

	// Page before current page
	if(($go - $p) < 1){ $davor = $go - 1;  }else { $davor = $p; }            

	// Page after current page
	if(($go + $p) > $seiten){ $danach = $seiten - $go; }else{ $danach = $p; }   
       
	$off = ($go - $davor);
                
	if ($go- $davor > 1){ // Define link for first page
		$first = 1;
		$links[] = "<a href=\"$pfad?go=$first&nonavi=".$_GET["nonavi"]."&ext_searchstring=".$_GET["ext_searchstring"]."\" title=\"Zur ersten Seite springen\">&laquo; Erste ...</a>\n";      
	}      

	if($go != 1){ // Define link "one page back"          
		$prev = $go-1;
		$links[] = "<a href=\"$pfad?go=$prev&nonavi=".$_GET["nonavi"]."&ext_searchstring=".$_GET["ext_searchstring"]."\" title=\"Eine Seite zurueck blaettern\"> &laquo;</a>\n";     
	}   
       
       
	for($i = $off; $i <= ($go + $danach); $i++){ // Create direct links

		if ($i != $go){ 
	
			$links[] = "<a href=\"$pfad?go=$i&nonavi=".$_GET["nonavi"]."&ext_searchstring=".$_GET["ext_searchstring"]."\">$i</a>\n";
        
		}elseif($i == $seiten) { // No link is needed for the current page
        
			$links[] = "<span class=\"current\">[ $i ]</span>\n";  
        
		}elseif($i == $go){ // No link is needed for the current page
  
			$links[] = "<span class=\"current\">[ $i ]</span>\n";
        
		} // close if $i      
	}                

	if($go != $seiten){ // Define link for next page       
		$next = $go+1;
		$links[] = "<a href=\"$pfad?go=$next&nonavi=".$_GET["nonavi"]."&ext_searchstring=".$_GET["ext_searchstring"]."\" title=\"Eine Seite weiter blaettern\"> &raquo; </a>\n";
	}      
    
	if($seiten - $go - $p > 0 ){ // Define link for last page
		$last = $seiten; 
		$links[] = "<a href=\"$pfad?go=$last&nonavi=".$_GET["nonavi"]."&ext_searchstring=".$_GET["ext_searchstring"]."\" title=\"Zur letzten Seite springen\">... Letzte &raquo;</a>\n";
	}      

	$start = ($go-1) * $datensaetze_pro_seite;


	$link_string = implode(" ", $links); // Combine all links to a single string
	// --- END: browse list ---
}

?>
</body>
</html>