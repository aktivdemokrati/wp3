<?php
if ( !current_user_can('edit_users') )
	wp_die(__('Cheatin&#8217; uh?'));

include( ADHOMESYS.'/ad-user-profile-resources.php' );
?>


<h3 id="membership">Medlemsuppgifter för partimedlemmar</h3>
<table class='form-table'>
  <tr><?php ad_input_bool($user,'ad_member','Är medlem i partiet','Ja') ?></tr>
  <tr><?php ad_input_text($user,'ad_member_id','Medlemsnummer') ?></tr>
  <tr><?php ad_input_text($user,'ad_personnummer','Personnummer','YYYYMMDD-XXXX') ?></tr>
  <tr><?php ad_input_date($user,'ad_member_payed','Medlemsavgift betald') ?></tr>
  <tr><?php ad_input_date($user,'ad_member_from','Medlemskap påbörjas','Senaste påbörjan') ?></tr>
  <tr><?php ad_input_date($user,'ad_member_to','Medlemskap avslutas') ?></tr>
  <tr><?php ad_input_bool($user,'ad_member_lifetime','Livstidsmedlemskap','Ja') ?></tr>
  <tr><?php ad_input_bool($user,'ad_folkbokf','Identitet verifierad','Ja') ?></tr>
  <tr><?php ad_input_bool($user,'ad_email_invalid','E-postadressen ur funktion', 'Ja') ?></tr>
  <tr><?php ad_input_bool($user,'inactive','Kontot inaktiverat', 'Ja') ?></tr>
</table>


<h3>För medlemsadmin. Ej synligt för användaren</h3>
<table class='form-table'>
  <tr><?php ad_input_textarea($user,'ad_memadmin_notes','Noteringar för medlemsadmin','') ?></tr>
  <tr><?php ad_input_textarea($user,'ad_memadmin_log','Loggbok för medlemsadmin','Kronologisk listning av händelser så som medlemsinbetalningar, etc. En per rad.') ?></tr>
</table>
<script type="text/javascript" charset="utf-8">
	if (window.location.hash == '#membership') {
		document.getElementById('membership').focus();
	}
</script>
