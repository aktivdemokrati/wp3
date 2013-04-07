function update_button(response)
{
    log('update_button');
    $ = jQuery;
    var button = $('#fb-auth');
    var button_text = $('#fb-auth .fb_button_text');
    var userinfo = $('#user-info');

    if( response.status === 'connected' )
    {
	//user is already logged in and connected
	button.hide;
	button.css('visibility','visible');
	button.fadeIn();
	button.attr('href',
		    jQuery.param.querystring( window.location.href,
					      'autologin=1'));
    }
    else if( response.status === 'not_authorized' )
    {
	button.hide;
	button.css('visibility','visible');
	button.unbind().click(fb_login);
	button.fadeIn();
    }
    else //Not logged in to facebook
    {
    }
}

function fb_login()
{
    log('FB.login');
    FB.login( fb_login_callback, {scope:'email,user_website,user_hometown'});
}

function fb_logout()
{
    FB.logout( function(response){
	$ = jQuery;
	var userinfo = $('#user-info');
	userinfo.html("");
    });
}

function fb_login_callback(response)
{
    $ = jQuery;

    if(response.authResponse)
    {
        $url =  "http://aktivdemokrati.se/wp-login.php?action=register&autologin=1";
        $redirect = $('#fb-auth').attr('redirect_to');
        if(!$redirect)
        {
            if( $('body.login').size() )
                $redirect = document.referrer;
            else
                $redirect = window.location.href;
        }

        $url =  "http://aktivdemokrati.se/wp-login.php?action=register&autologin=1&redirect_to=" + encodeURIComponent($redirect);
        window.location.href = $url;
    }
    else
    {
	log('User cancelled login or did not fully authorize.');
    }
}

function ad_fb_register_form(response)
{
    log('Checking if we should draw FB register form');
    if( response.status === 'not_authorized' )
    {
	$('#registerform p').hide();
        $('#registerform #fb-register p').show();
        $('#fb-register').show();
        $('#registerform').css('padding-bottom',0);
        $('#wp-auth').click(ad_fb_register_form_show_standard);
    }
    else
    {
        $('#registerform p').show();
    }
}

function ad_fb_register_form_show_standard()
{
    $('#registerform').css('padding-bottom','');
    $('#fb-register').hide();
    $('#registerform #fb-register p').hide();
    $('#registerform p').show();
}
	 
function log(stuff)
{
    if( typeof console != 'undefined' )
    {
        console.log(stuff);
    }
}
     

function fb_init()
{
    $ = jQuery;
    // run once with current status and whenever the status changes
    FB.getLoginStatus(update_button);
//    FB.Event.subscribe('auth.statusChange', update_button);

//    alert("Logged in: " + window.ad_wp_logged_in);

    if( !window.ad_wp_logged_in )
    FB.Event.subscribe('auth.login', function(response) {
        var token = response.authResponse.accessToken;
	
	$('input').attr('disabled', 'disabled');
	$('#fb-auth').hide();

	$("#dim").css("height", $(document).height());
	$("#dim").show();
	$('#spinner-center').fadeIn(1800);
	$(window).bind("resize", function(){
	    $("#dim").css("height", $(window).height());
	});

        window.location.href =
            jQuery.param.querystring( window.location.href,
                                      'fb='+token );
    });

/*
    FB.Event.subscribe('auth.logout', function(response) {
//        alert("RELOAD for logout");
//        window.location.reload();
    });
*/

    if( typeof window.fb_init_local == 'function') {
        fb_init_local();
    }

    FB.XFBML.parse();

//    $ = jQuery;
//    var userinfo = $('#user-info');
    log("AD v1.3");
}
