<?php
$control = <<<SUPERCONTROL
<input type="hidden" name="$name" value="$value">
<select class="select-custom-fwd">
	<option data-target="1" value="Headquarters">Headquarters</option>
	<option data-target="2" value="Insurance International">Insurance International</option>
	<option data-target="3" value="Investment Management">Investment Management</option>
	<option data-target="4" value="Nationale-Nederlanden">Nationale-Nederlanden</option>
</select>
<div>
	<br/>
	<select data-value="1">
		<option value="CC&amp;A">CC&amp;A</option>
		<option value="Finance">Finance</option>
		<option value="Risk">Risk</option>
		<option value="Legal &amp; Compliance">Legal &amp; Compliance</option>
		<option value="Global Procurement">Global Procurement</option>
		<option value="HR">HR
		<option value="IT Eurasia">IT Eurasia
		<option value="Cas Eurasia">Cas Eurasia</option>
		<option value="Capital Management">Capital Management</option>
	</select>
	<select data-value="2">
		<option value="Poland">Poland</option>
		<option value="Czech Republic">Czech Republic</option>
		<option value="Slovakia">Slovakia</option>
		<option value="Hungary">Hungary</option>
		<option value="Romania">Romania</option>
		<option value="Greece">Greece</option>
		<option value="Spain">Spain</option>
		<option value="Belgium">Belgium</option>
		<option value="Bulgary">Bulgary</option>
		<option value="Luxembourgh">Luxembourgh</option>
	</select>
	<select data-value="3">
		<option value="Investment Management">Investment Management</option>
	</select>
	<select data-value="4">
		<option value="Bank">Bank</option>
		<option value="Leven">Leven</option>
		<option value="Schade &amp Inkomen">Schade &amp Inkomen</option>
		<option value="Intermediaire Zaken">Intermediaire Zaken</option>
	</select>
</div>
SUPERCONTROL;
?>