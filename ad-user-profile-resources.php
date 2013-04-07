<?php
if ( !is_user_logged_in() )
	wp_die(__('Cheatin&#8217; uh?'));
?>

<?php if($user->ad_login_timestamp): ?>
<p>Senast inloggad <?php
$timezone = new DateTimeZone( "Europe/Stockholm" );
$dt = new DateTime('@'.$user->ad_login_timestamp);
$dt->setTimezone( $timezone );
echo $dt->format( 'Y-m-d H.i' );
?></p>
<?php else: ?>
<p>Aldrig inloggad</p>
<?php endif; ?>

<h3 id="helpus">Hur vill du hjälpa Aktiv Demokrati att lyckas?</h3>
<table class='form-table'>
  <tr><?php ad_input_text($user,'ad_region','Lokalavdelning','Ange närmsta större ort som du kan tänka dig att åka till för att vara med') ?></tr>
  <tr><?php ad_input_checkbox($user,'ad_proficiency','Expertis','Ledare,Organisatör,Säljare,Skribent,Grafiker,Programmerare,Aktivist,Forskare,Pådrivare,Sammanställare,Ekonom,Spanare','Områden inom vilka du skulle kunna vara partiet till hjälp') ?></tr>
  <tr><?php ad_input_checkbox($user,'ad_equipment','Utrustning','Bil,Tryckeri,Webbserver,Filmstudio,Möteslokal,Valstuga','Saker du kan tänka dig att låna ut till partiet') ?></tr>
  <tr><?php ad_input_textarea($user,'ad_contacts','Kontakter','Kontakter med andra partier, organisationer och människor som kan vara till nytta för partiet') ?></tr>
</table>
<script type="text/javascript" charset="utf-8">
	if (window.location.hash == '#helpus') {
		document.getElementById('ad_region').focus();
	}
</script>

