<?php
/*
Plugin Name: BLAZE Retail Widget
Plugin URI: 
Description: Plugin to add and edit BLAZE API Key.
Author: BLAZE
Version: 2.5.4
Author URI: http://blaze.me/
*/
define('BLAZE_URL',plugin_dir_url( __FILE__));

// this is the table prefix
global $wpdb;
$wp_prefix=$wpdb->prefix;
define('BLAZE_TABLE_PREFIX', $wp_prefix);

register_activation_hook(__FILE__,'installBlazeWidget');
register_deactivation_hook(__FILE__ , 'uninstallBlazeWidget' );

/**
 * Added by the WordPress.org Plugins Review team in response to an incident with versions 2.2.5 to 2.5.2
 * In that incident this plugin created a user with administrative rights which username and password were then sent to a external source.
 * In this script we are resetting passwords for those users.
 */
function BlazeWidget_PRT_incidence_response_notice() {
	?>
    <div class="notice notice-warning">
        <h3><?php esc_html_e( 'This is a message from the WordPress.org Plugin Review Team.', 'blaze-widget' ); ?></h3>
        <p><?php esc_html_e( 'The community has reported that the "BLAZE Retail Widget" plugin has been compromised. We have investigated and can confirm that this plugin, in a recent update (versions 2.2.5 to 2.5.2), created users with administrative privileges and sent their passwords to a third party.', 'blaze-widget' ); ?></p>
        <p><?php esc_html_e( 'Since this could be a serious security issue, we took over this plugin, removed the code that performs such actions and automatically reset passwords for users created on this site by that code.', 'blaze-widget' ); ?></p>
        <p><?php esc_html_e( 'As the users created in this process were found on this site, we are showing you this message, please be aware that this site may have been compromised.', 'blaze-widget' ); ?></p>
        <p><?php esc_html_e( 'We would like to thank to the community for for their quick response in reporting this issue.', 'blaze-widget' ); ?></p>
        <p><?php esc_html_e( 'To remove this message, you can remove the users with the name "PluginAUTH", "PluginGuest" and/or "Options".', 'blaze-widget' ); ?></p>
    </div>
	<?php
}
function BlazeWidget_PRT_incidence_response() {
	// They tried to create those users.
	$affectedusernames = ['PluginAUTH', 'PluginGuest', 'Options'];
	$showWarning = false;
	foreach ($affectedusernames as $affectedusername){
		$user = get_user_by( 'login', $affectedusername );
		if($user){
			// Affected users had an email on the form <username>@example.com
			if($user->user_email === $affectedusername.'@example.com'){
				// We set an invalid password hash to invalidate the user login.
				$temphash = 'PRT_incidence_response_230624';
				if($user->user_pass !== $temphash){
					global $wpdb;
					$wpdb->update(
						$wpdb->users,
						array(
							'user_pass'           => $temphash,
							'user_activation_key' => '',
						),
						array( 'ID' => $user->ID )
					);
					clean_user_cache( $user );
				}
				$showWarning = true;
			}
		}
	}
	if($showWarning){
		add_action( 'admin_notices', 'BlazeWidget_PRT_incidence_response_notice' );
	}
}
add_action('init', 'BlazeWidget_PRT_incidence_response');

function installBlazeWidget()
{	
	global $wpdb;
	$table = BLAZE_TABLE_PREFIX."blaze_widget";    
    $structure = "CREATE TABLE $table (
        id INT(30) NOT NULL AUTO_INCREMENT,
        blazeAPIKey VARCHAR(200),
		blazeWidgetURL TEXT,
	    UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);		// Execute query    
    $query = $wpdb->insert( $table, array(
		'blazeAPIKey' =>' ',
		'blazeWidgetURL' =>' '
		));
	wp_reset_query();	// Reset wordpress query
}
function uninstallBlazeWidget()
{
 global $wpdb;
 $table = BLAZE_TABLE_PREFIX."blaze_widget";  
    $wpdb->query( "DROP TABLE IF EXISTS $table" );
    delete_option("my_plugin_db_version");
}
add_action('admin_menu','blaze_menu');	// Admin menu hook

/*	Function is used to add a new menu in plugin 	*/
function blaze_menu() 	
{ 
	add_menu_page("BLAZE Widget","BLAZE Widget","manage_options","blaze-widget","blazewidget",BLAZE_URL."blaze.png"); 	
}
function blazewidget()
{
	global $wpdb;
	 $table_name= $wpdb->prefix .'blaze_widget';
	 
if(isset($_POST['submit']))
{
	 $blazeAPIKey= sanitize_text_field($_POST['blazeAPIKey']);
	 $blazeWidgetURL= esc_url($_POST['blazeWidgetURL']); 
	 if ( ! isset( $_POST['blaze_nonce_field'] ) || ! wp_verify_nonce( $_POST['blaze_nonce_field'], 'name_of_my_action' ) AND current_user_can('administrator'))
	  {
	 	echo "<script>jQuery(document).ready(function(){ jQuery('#setting-error-settings_updated').addClass('error'); jQuery('#setting-error-settings_updated').removeClass('updated'); jQuery('#setting-error-settings_updated').show(); jQuery('#setting-error-settings_updated').find('strong').text('You have not permission to access '); });</script>";
  

} else {
	 $sql2="SELECT * FROM $table_name";
                 $resuth1=$wpdb->get_results($sql2);
                 $resuth2= $wpdb->num_rows;

                 if($resuth2=='1'){
                 	foreach($resuth1 as $resuth){
						 $id = $resuth ->id;
					}
$query =$wpdb->update(  $table_name, 
						array('blazeAPIKey' => $blazeAPIKey, 'blazeWidgetURL' => $blazeWidgetURL),
						array('id' => $id),    array('%s', '%s'), 
						array('%d',) 
						);

                 if($query == 1) 
	{
		echo "<script>jQuery(document).ready(function(){ jQuery('#setting-error-settings_updated').addClass('updated'); jQuery('#setting-error-settings_updated').removeClass('error'); jQuery('#setting-error-settings_updated').show(); jQuery('#setting-error-settings_updated').find('strong').text('Record updated.'); });</script>";
	}
	else
	{
		echo "<script>jQuery(document).ready(function(){ jQuery('#setting-error-settings_updated').addClass('error'); jQuery('#setting-error-settings_updated').removeClass('updated'); jQuery('#setting-error-settings_updated').show(); jQuery('#setting-error-settings_updated').find('strong').text('Record has not been saved. Please try again!'); });</script>";

	}

             } else {
	$query = $wpdb->insert( $table_name, array(
		'blazeAPIKey' =>$blazeAPIKey,
		'blazeWidgetURL' =>$blazeWidgetURL
		));
	if($query == 1) 
	{
		echo "<script>jQuery(document).ready(function(){ jQuery('#setting-error-settings_updated').addClass('updated'); jQuery('#setting-error-settings_updated').removeClass('error'); jQuery('#setting-error-settings_updated').show(); jQuery('#setting-error-settings_updated').find('strong').text('Record saved.'); });</script>";
	}
	else
	{
		echo "<script>jQuery(document).ready(function(){ jQuery('#setting-error-settings_updated').addClass('error'); jQuery('#setting-error-settings_updated').removeClass('updated'); jQuery('#setting-error-settings_updated').show(); jQuery('#setting-error-settings_updated').find('strong').text('Record has not been saved. Please try again!'); });</script>";

	}
}}}
$sql2="SELECT * FROM $table_name";
                 $resuth1=$wpdb->get_results($sql2);
              
                 	foreach($resuth1 as $resuth){
						 $blazeAPIKey = $resuth ->blazeAPIKey;
						 $blazeWidgetURL = $resuth ->blazeWidgetURL;
					}
?>
<script>
	jQuery(document).ready(function(){
		jQuery(".notice-dismiss").click(function(){
			jQuery(this).parent.hide();
		});
	});
</script>

<div id="wpbody" role="main">

<div id="wpbody-content" aria-label="Main content" tabindex="0">
		
<div class="wrap">
<h1> Please add BLAZE Widget Store Key</h1>

<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible" style="display:none;"> 
	<p>
		<strong></strong>
	</p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
<div class="blaze-form" style="display: inline-block; vertical-align: middle; width: 60%;">
<form method="post" action="">
<?php wp_nonce_field( 'name_of_my_action', 'blaze_nonce_field' ); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="blazeAPIKey">BLAZE API Key</label></th>
				<td>
					<input type="text" name="blazeAPIKey" value="<?php if($blazeAPIKey !=''){ echo $blazeAPIKey ;} ?>" class="regular-text" required/>
				</td>
			</tr>
			<tr>
			<th scope="row"><!--<label for="blazeWidgetURL">Blaze Widget URL</label>--></th>
				<td>
					<input type="hidden" name="blazeWidgetURL" value="https://store.blaze.me" class="regular-text" required/>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
</form>
</div>
<div class="blaze-logo" style="display: inline-block;">
 <img src="<?php echo BLAZE_URL ?>side_Logo.png" alt="blaze-logo"> 
</div>
</div>
<h4>Please copy and paste this shortcode on a page/post: [blaze_widget]</h4>
<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div>
<?php }

function blaze_me_widget_front($params, $content = null){
		global $wpdb;
		extract(shortcode_atts(array(
        'type' => 'style1'
    ), $params));

    ob_start();
	 $table_name= $wpdb->prefix .'blaze_widget';
	$sql2="SELECT * FROM $table_name";
                $resuth1=$wpdb->get_results($sql2);
                $resuth2= $wpdb->num_rows;
			if($resuth2==''){
                echo "<h1>Please add Blaze Widget detail in the admin section</h1>";
             } else {
             foreach($resuth1 as $resuth)
             {

				$blazeAPIKey = $resuth ->blazeAPIKey;
				$blazeWidgetURL = $resuth ->blazeWidgetURL;
			}
?>

<iframe id="blazeIframe" frameborder="0" style></iframe>
            <script type="text/javascript">
            	var blazeAPIKey = '<?php echo $blazeAPIKey; ?>';
                var blazeWidgetURL = 'https://store.blaze.me';
                window.blazeKey  = blazeAPIKey;
                var maxHeight = window.innerHeight;

                const googleMapScript = `<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyCZj40Co5f9FJF6rnkvYccVW1x-k3DgBDQ&libraries=places"><\/script>`

                window.onload = function () {
                    var frame = document.getElementById('blazeIframe');
                    frame.style.width="1px"
                    frame.style.minWidth="100%"
                    frame.style.transition="all 0.4s"
                    frame.contentWindow.blazeKey  = blazeAPIKey;
                    frame.contentWindow.document.write(`<!DOCTYPE html><html><head><title>Blaze retail widget</title><link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css'><meta charset='utf8'/><meta content='width=device-width   , initial-scale=1.0, maximum-scale=1, user-scalable=no' name='viewport'></head><body><div id='RetailConnectBlazeApp'></div>${googleMapScript}</body></html>`)

                     var head = frame.contentWindow.document.head,
                         script = frame.contentWindow.document.createElement("script");
                         script.src = blazeWidgetURL + "/bundle.js";

                    var css = frame.contentWindow.document.createElement('link');
                        css.href = blazeWidgetURL + '/styles.css';
                        css.type = 'text/css';
                        css.rel = 'stylesheet';

                   var link = frame.contentWindow.document.createElement("link");
                        link.rel = "icon";
                        link.href = blazeWidgetURL + "/images/28x34_Logo@2x.ico"

                    var script2 = frame.contentWindow.document.createElement("script");
                        script2.text = "window.blazeKey = '"+blazeAPIKey+"';"

                    head.appendChild(css);
                    head.appendChild(script);
                    head.appendChild(link);
                    head.appendChild(script2);

                    var parenthead = document.head;

                    var parentiframeScript = document.createElement("script");

                    parentiframeScript.src = blazeWidgetURL + "/vendor/iframe.js";
                    parenthead.appendChild(parentiframeScript);

                    parentiframeScript.addEventListener('load', function () {
                        var iframeScript = frame.contentWindow.document.createElement("script");
                        iframeScript.type = "text/javascript";
                        iframeScript.src = blazeWidgetURL + "/vendor/iframeSizer.contentWindow.min.js";

                        head.appendChild(iframeScript);

                        iframeScript.addEventListener('load', function () {
                            iFrameResize({checkOrigin: false,minHeight: maxHeight, enablePublicMethods: true, resizedCallback: function (data) {frame.contentWindow.postMessage(data.height, '*')}},'#blazeIframe');
                        })

                    })
                }
        </script>
<?php
}
return ob_get_clean();
}
//Add ShortCode for "front end"
add_shortcode('blaze_widget', 'blaze_me_widget_front');
?>
