function AddVoucherToggleExp()
{
	if(document.voucherform.start_expire.value=="now")
	{
		document.voucherform.d.disabled=false;
		document.voucherform.h.disabled=false;
		document.voucherform.m.disabled=false;
		document.voucherform.e_d.disabled=true;
		document.voucherform.e_h.disabled=true;
		document.voucherform.e_m.disabled=true;
		
		document.voucherform.d.className="formstyle";
		document.voucherform.h.className="formstyle";
		document.voucherform.m.className="formstyle";
		document.voucherform.e_d.className="roinput";
		document.voucherform.e_h.className="roinput";
		document.voucherform.e_m.className="roinput";
	} else {
		document.voucherform.d.disabled=true;
		document.voucherform.h.disabled=true;
		document.voucherform.m.disabled=true;
		document.voucherform.e_d.disabled=false;
		document.voucherform.e_h.disabled=false;
		document.voucherform.e_m.disabled=false;
		
		document.voucherform.d.className="roinput";
		document.voucherform.h.className="roinput";
		document.voucherform.m.className="roinput";
		document.voucherform.e_d.className="formstyle";
		document.voucherform.e_h.className="formstyle";
		document.voucherform.e_m.className="formstyle";
	}
}