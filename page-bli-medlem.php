<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
get_header(); ?>
<script src="<?php echo(ADHOMEURL) ?>/js/personnummer.js"></script>
<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<?php get_template_part( 'loop-header' ); ?>
<?php responsive_entry_before(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>       
<?php responsive_entry_top(); ?>

<?php if($current_user->ad_member){ ?>
<h1 class="post-title">Förnya medlemskapet</h1>

<p>Du betalade din förra medlemsavgift
<?php echo $current_user->ad_member_payed ?>
</p>

<?php } else { ?>
<h1 class="post-title">Bli medlem</h1>
<?php } ?>

<div class="do123">
<?php if( !is_user_logged_in() ){ ?>
<p class="step"><a name="do1">1.</a> <a href="/wp-login.php?redirect_to=/bli-medlem/"><span class="login">Logga in</span></a></p>
<p class="step disabled"><span>2.</span> Betala <span class="coin">valfritt belopp</span></p>
<p class="step disabled"><span>3.</span> Berätta om dig själv</p>
<?php } else {  ?>
<p class="step"><a name="do1">1.</a> <span class="done">✔</span><span class="login disabled">Logga in</span></p>
<p class="step"><a name="do2">2.</a> <a href=#" onclick="$('.ad-pay').toggle();return false;">Betala <span class="coin">valfritt belopp</span></a></p>
<div class="ad-pay info expand">via <a target="ad_payment" href="https://www.payson.se/SendMoney/?De=Medlemskap+med+valfritt+belopp%21&Se=kontakt%40aktivdemokrati.se&Cost=0&ShippingAmount=0%2c00&Sp=1">payson</a>, <a href="#" onclick="$('.ad-plusgiro').toggle();return false;">plusgiro</a> <span class="ad-plusgiro expand important">1412890-4</span> eller <a href="#" onclick="$('.ad-posten').toggle();return false;">posten</a>.
<span class="ad-posten expand"><br>
Fredrik Liljegren<br>
Hökås gård<br>
511 92  HYSSNA</span>
<span class="ad-plusgiro ad-posten expand"><br>Ange Användarnamn. Ge oss det stöd du kan. Exempelvis 350 kr per år.</span>
</div>
<p class="step"><a name="do3">3.</a> <a href="#" onclick="$('.ad-tell').toggle();return false;">Berätta om dig själv</a></p>
<?php if( $current_user->ad_personnummer ){ ?>
<div class="ad-tell info">Fyll i din adress och övriga uppgifter i <a href="/wp-admin/profile.php">din profil</a>
</div>
<?php
   } else {
    if( $_GET['pn'] ) {
      update_user_meta($user_ID, 'ad_personnummer', $wpdb->prepare($_GET['pn']));
    }
?>
<div class="ad-tell info expand">Personnummer <span id="pn-status"></span><form name="fpn" onsubmit="return validatePnForm()"><input name="pn"><input type="submit" value="Spara"></form></span>
</div>

<?php } ?>

<?php } ?>
<p class="more clear"><a href="#" onclick="$('.post-entry').toggle();return false;">Mer information...</a></p>
<br class="clear">
</div>


                
<?php if ( has_post_thumbnail( $post->ID ) )
echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' ); ?>
<div class="post-entry expand">
<?php the_content(__('Read more &#8250;', 'responsive')); ?>
<?php wp_link_pages(array('before' => '<div class="pagination">' . __('Pages:', 'responsive'), 'after' => '</div>')); ?>
</div><!-- end of .post-entry -->
<?php get_template_part( 'post-data' ); ?>
<?php responsive_entry_bottom(); ?>      
</div><!-- end of #post-<?php the_ID(); ?> -->       
<?php responsive_entry_after(); ?>
<?php responsive_comments_before(); ?>
<?php comments_template( '', true ); ?>
<?php responsive_comments_after(); ?>
<?php endwhile; 
get_template_part( 'loop-nav' ); 
else : 
get_template_part( 'loop-no-posts' ); 
endif; 
?>  
</div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
