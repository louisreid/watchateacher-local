<?php
/*
Plugin Name: BuddyPress Members only
Plugin URI: https://membersonly.top/features/
Description: Only registered users can view your site, non members can only see a login/home page with no registration options. More amazing features? Login and Logout auto redirect to Referer page or Certain page based on user roles,  Restricts your BP standard Components and customized  Components to users,enable page level members only protect, only protect your buddypress and open all other WP area to users... etc, Get <a href='https://membersonly.top' target='blank'>BP Members Only Pro</a> now.  
Version: 1.7.3
Author: Tomas Zhu: <a href='http://membersonly.top' target='_blank'>BP Members Only Pro</a>
Author URI: https://membersonly.top/features/
Text Domain: bp-members-only
License: GPLv3 or later
Copyright 2017  Tomas Zhu  (email : expert2wordpress@gmail.com)
This program comes with ABSOLUTELY NO WARRANTY;
 https://www.gnu.org/licenses/gpl-3.0.html
 https://www.gnu.org/licenses/quick-guide-gplv3.html
*/
if (!defined('ABSPATH'))
{
	exit;
}

ob_start();
add_action('admin_menu', 'bp_members_only_option_menu');

/**** localization ****/
add_action('plugins_loaded','bp_members_only_load_textdomain');

function bp_members_only_load_textdomain()
{
	load_plugin_textdomain('bp-members-only', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
}

function bp_members_only_option_menu()
{

   add_menu_page(__('Buddypress Members Only', 'bp-members-only'), __('Buddypress Members Only', 'bp-members-only'), 'manage_options', 'bpmemberonly', 'buddypress_members_only_setting');
   add_submenu_page('bpmemberonly', __('Buddypress Members Only','bp-members-only'), __('Buddypress Members Only','bp-members-only'), 'manage_options', 'bpmemberonly', 'buddypress_members_only_setting');
}

function buddypress_members_only_setting()
{
		global $wpdb;
		$m_bpmoregisterpageurl = get_option('bpmoregisterpageurl');

		if (isset($_POST['bpmosubmitnew']))
		{
			if (isset($_POST['bpmoregisterpageurl']))
			{
				$m_bpmoregisterpageurl = $wpdb->escape($_POST['bpmoregisterpageurl']);
			}
				
			update_option('bpmoregisterpageurl',$m_bpmoregisterpageurl);
			if (isset($_POST['bpopenedpageurl']))
			{
				$bpopenedpageurl = trim($_POST['bpopenedpageurl']);
				if (strlen($bpopenedpageurl) == 0)
				{
					
				}
				else 
				{
					update_option('saved_open_page_url',$bpopenedpageurl);
				}
			}

			$bpmoMessageString =  __( 'Your changes has been saved.', 'bp-members-only' );
			buddypress_members_only_message($bpmoMessageString);
		}
		echo "<br />";

		$saved_register_page_url = get_option('bpmoregisterpageurl');
		?>

<div style='margin:10px 5px;'>
<div style='float:left;margin-right:10px;'>
<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/buddypress-members-only/images/new.png' style='width:30px;height:30px;'>
</div> 
<div style='padding-top:5px; font-size:22px;'> <i></>Buddypress Members Only Setting:</i></div>
</div>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body"  style="width:60%;">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding-top:10px;padding-bottom:10px;'><span>
									<?php 
											echo  __( 'Option Panel:', 'bp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
										<form id="bpmoform" name="bpmoform" action="" method="POST">
										<table id="bpmotable" width="100%">
										<tr>
										<td width="100%" style="padding: 20px;">
										<p>
										<?php echo  __( 'Register Page URL:', 'bp-members-only' ); ?>
										</p>
										<input type="text" id="bpmoregisterpageurl" name="bpmoregisterpageurl"  style="width:500px;" size="70" value="<?php  echo $saved_register_page_url; ?>">
										</td>
										</tr>
										
										<tr style="margin-top:30px;">
										<td width="100%" style="padding: 20px;">
										<p>
										<?php echo  __( 'Opened Page URLs:', 'bp-members-only' ); ?>
										</p>
										<?php 
										$urlsarray = get_option('saved_open_page_url'); 
										?>
										<textarea name="bpopenedpageurl" id="bpopenedpageurl" cols="70" rows="10" style="width:500px;"><?php echo $urlsarray; ?></textarea>
										<p><font color="Gray"><i><?php echo  __( 'Enter one URL per line please.', 'bp-members-only' ); ?></i></p>
										<p><font color="Gray"><i><?php echo  __( 'These pages will opened for guest and guest will not be directed to register page.', 'bp-members-only' ); ?></i></p>					
										</td>
										</tr>
										</table>
										<br />
										<input type="submit" id="bpmosubmitnew" name="bpmosubmitnew" value=" Submit " style="margin:1px 20px;">
										</form>
										
										<br />
									</div>
								</div>
							</div>
						</div>
					</div>
				
					<div id="post-body"  style="width:40%; float:right;">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
							
							
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px; !important'>
									<span>
									<?php 
											//echo  __( 'About This Plugin', 'bp-members-only' );
									
									?>
									<a class="" target="_blank" href="https://membersonly.top/features/">Members Only Pro Features</a>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
							<div class="inside">
									<ul>
									<li>
										* Login and Logout auto redirect based on user roles
									</li>
									<li>
										* Options to only protect your buddypress pages, so other section on your wordpress site will be open to the guest users.
									</li>
									<li>
										* Enable page level protect, when you edit a post, you can choose setting it as a members only page or not.
									</li>
									<li>
										* If you disable buddypress, our plugins will detect it automatically and continue to protect your wordpress pages.
									</li>
									<li>
										* Restricts your BP standard Components to Logged in/Registered members only, you can choose which components will open to guest user, or which components will only opened to logged in users
									</li>
									<li>
										* 	Restricts your BP customized Components to Logged in/Registered members only, you can setting as more as customized components to open to guest users.
										……
									</li>
									<li>
									<b>more feature can be found at: <a class="" target="_blank" href="https://membersonly.top/features/">https://membersonly.top/features/</a></b>
									</li>
										</div>									
									</div>
								</div>

								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
											echo  __( 'About This Plugin', 'bp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
							<div class="inside">
									<ul>
									<li>
										* <a class="" target="_blank" href="https://membersonly.top/features/">Plugin Homepage, Help / FAQ</a>
									</li>
									<li>
								* <a class=""  target="_blank" href="https://membersonly.top/contact-us/">Suggest a Feature, Report a Bug? Need Customize Plugin?</a>
								</li>
								<li>								
								* <a class=""  target="_blank" href="https://tomas.zhu.bz/category/my-share/">Sign UP for Free BuddyPress / WordPress Tips MailChimp List</a>
								</li>
								<li>								
								* <a class=""  target="_blank" href="https://membersonly.top/js-support-ticket-controlpanel/">Support</a>
								</li>
									
						</div>									
									
									</div>
								</div>
																
								
								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
											echo  __( 'BuddyPress Wordpress Tips Feed:', 'bp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
						<?php 
							wp_widget_rss_output('https://tomas.zhu.bz/feed/', array(
							'items' => 3, 
							'show_summary' => 0, 
							'show_author' => 0, 
							'show_date' => 1)
							);
						?>
										<br />
									</div>
								</div>
							</div>
						</div>
											
					</div>
					<div style='clear:both'></div>					
		    	</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<br />

		
		
		<?php
		}				

	
function buddypress_members_only_message($p_message)
{

	echo "<div id='message' class='updated fade' style='line-height: 30px;margin-left: 0px;'>";

	echo $p_message;

	echo "</div>";

}

function buddypress_only_for_members()
{
	if (is_front_page()) return;
	if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
	{
		if ( bp_is_register_page() || bp_is_activation_page() )
		{
			return;
		}
	}
	
	$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$current_url = str_ireplace('http://','',$current_url);
	$current_url = str_ireplace('https://','',$current_url);
	$current_url = str_ireplace('ws://','',$current_url);
	$current_url = str_ireplace('www.','',$current_url);
	$saved_register_page_url = get_option('bpmoregisterpageurl');

	$saved_register_page_url = str_ireplace('http://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('https://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('ws://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('www.','',$saved_register_page_url);
	
	if (stripos($saved_register_page_url, $current_url) === false)
	{

	}
	else 
	{
		return;
	}
	
	// check is opened pages
	$saved_open_page_option = get_option('saved_open_page_url');

	$saved_open_page_url = explode("\n", trim($saved_open_page_option));

	if ((is_array($saved_open_page_url)) && (count($saved_open_page_url) > 0))
	{
		$root_domain = get_option('siteurl');
		foreach ($saved_open_page_url as $saved_open_page_url_single)
		{
			// start 1.6
			$saved_open_page_url_single = trim($saved_open_page_url_single); 

			if (reserved_url($saved_open_page_url_single) == true)
			{
				continue;
			}
			// end 1.6			
			$saved_open_page_url_single = pure_url($saved_open_page_url_single);

			
			if (stripos($current_url,$saved_open_page_url_single) === false)
			{

			}
			else 
			{

				return;
			}
		}
	}

	if ( is_user_logged_in() == false )
	{
		if (empty($saved_register_page_url))
		{
			$current_url = $_SERVER['REQUEST_URI'];
			//$redirect_url = wp_login_url( get_option('siteurl').$current_url );
			$redirect_url = wp_login_url( );
			header( 'Location: ' . $redirect_url );
			die();			
		}
		else 
		{
			$saved_register_page_url = 'http://'.$saved_register_page_url;
			header( 'Location: ' . $saved_register_page_url );
			die();
		}
	}
}

function pure_url($current_url)
{
	if (empty($current_url)) return false;

	$current_url_array = parse_url($current_url); // 1.6

	
	
	// 1.3
	$current_url = str_ireplace('http://','',$current_url);
	// start 1.6
	$current_url = str_ireplace('https://','',$current_url);
	$current_url = str_ireplace('ws://','',$current_url);
	// end 1.6
		
	$current_url = str_ireplace('www.','',$current_url);
	$current_url = trim($current_url);
	return $current_url;
}

// 1.6
function reserved_url($url)
{
	$home_page = get_option('siteurl');
	$home_page = pure_url($home_page);
	$url = pure_url($url);
	if ($home_page == $url)
	{
		return true;
	}
	else
	{
		return false;
	}
} 


if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
{
	add_action('wp','buddypress_only_for_members');
}
else 
{
	add_action('wp_head','buddypress_only_for_members');
}