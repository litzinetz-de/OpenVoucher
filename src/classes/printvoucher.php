<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
	// Page header
	function Header()
	{
		// Logo
		$this->Image('../graphics/logo-small.png',10,6,15);
		// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Move to the right
		$this->Cell(80);
		// Title
		$this->Cell(30,10,'OpenVoucher',0,0,'C');
		// Line
		$this->Line(0,25,220,25);
	}

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

class printvoucher
{
	private $pdf;
	private $vouchertype;
	private $voucherlist;
	
	// vouchertype: small (many vouchers per page) or big (one voucher per page)
	// voucherlist: array of voucher IDs to print
	function __construct($vouchertype,$voucherlist)
	{
		$this->pdf=new PDF();
		$this->vouchertype=$vouchertype;
		$this->voucherlist=$voucherlist;
		
		$this->pdf = new PDF();
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',10);
		
		if($vouchertype=='small') // Small vouchers
		{
			$this->pdf->SetXY(3,30);
			$j=1; // cols
			$k=1; // rows
			for($i=0;$i<count($voucherlist);$i++)
			{
				if($j==6)
				{
					$this->pdf->SetXY(3,$this->pdf->GetY()+20);
					$j=1;
					$k++;
				}
				if($k==10)
				{
					// page break
					$k=1;
				}
				$this->pdf->Cell(40,20,'',1,0);
				$this->pdf->SetX($this->pdf->GetX()-40);
				$this->pdf->Cell(40,10,'Voucher ID:',0,2);
				$this->pdf->Cell(40,10,$voucherlist[$i],0,0);
				$this->pdf->SetXY($this->pdf->GetX(),$this->pdf->GetY()-10);
				$j++;
			}
		}
		
		$this->pdf->Output();
	}
}
?>