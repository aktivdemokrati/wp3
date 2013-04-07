<?php
if ( !is_user_logged_in() )
	wp_die(__('Cheatin&#8217; uh?'));

include( ADHOMESYS.'/ad-user-profile-resources.php' );
?>


<h3>Medlemsuppgifter för partimedlemmar</h3>
<table class='form-table'>
  <tr><?php ad_input_label('ad_member','Är medlem i partiet') ?>
  <td><?php if($user->ad_member){echo'Ja';}else{echo'Nej';} ?></td></tr>
  <tr><?php ad_input_label('ad_member_id','Medlemsnummer') ?>
  <td><?php echo $user->ad_member_id ?></td></tr>
  <tr><?php ad_input_label('ad_personnummer','Personnummer') ?>
  <td><?php echo $user->ad_personnummer ?></td></tr>
  <tr><?php ad_input_label('ad_member_payed','Medlemsavgift betald') ?>
  <td><?php echo $user->ad_member_payed ?></td></tr>
  <tr><?php ad_input_label('ad_member_from','Medlemskap påbörjas') ?>
  <td><?php echo $user->ad_member_from ?></td></tr>
  <tr><?php ad_input_label('ad_member_to','Medlemskap avslutas') ?>
  <td><?php echo $user->ad_member_to ?></td></tr>
<?php if($user->ad_member_lifetime):?>
  <tr><?php ad_input_label('ad_member_lifetime','Livstidsmedlemskap') ?><td>Ja</td></tr>
<?php endif; ?>
  <tr><?php ad_input_label('ad_folkbokf','Identitet verifierad') ?>
  <td><?php if($user->ad_folkbokf){echo'Ja';}else{echo'Nej';} ?></td></tr>
  <tr><?php ad_input_label('ad_email_invalid','E-postadressen ur funktion', 'Ja') ?>
  <td><?php if($user->ad_email_invalid){echo'Ja';}else{echo'Nej';} ?></td></tr>
  <tr><?php ad_input_label('inactive','Kontot inaktiverat', 'Ja') ?>
  <td><?php if($user->inactive){echo'Ja';}else{echo'Nej';} ?></td></tr>
</table>
