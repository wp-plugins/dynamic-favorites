<?php
/*
Plugin Name: Dynamic Favorites
Plugin URI: http://sivel.net/wordpress/
Description: Populates the admin favorites drop down menu, introduced in WP 2.7, with links based on actual page accesses. Lists the pages you actually use the most.
Author: Matt Martz
Version: 1.0
Author URI: http://sivel.net/
*/

// Only run the code if we are in the admin
if ( is_admin() ) :

class dynamic_favorites {
	// Table Post Fix
	var $postfix = 'favorites';

	// Action/Filter/Activation Hooks
	function dynamic_favorites() {
		add_action('admin_head', array(&$this, 'update'));
		add_action('delete_user', array(&$this, 'delete'));
		add_action('admin_menu', array(&$this, 'add_page'));
		add_action('personal_options', array(&$this, 'user_edit'));
		add_action('personal_options_update', array(&$this, 'user_update'));
		add_action('edit_user_profile_update', array(&$this, 'user_update'));
		add_filter('favorite_actions', array(&$this, 'populate'));
		register_activation_hook(__FILE__, array(&$this, 'init'));
	}

	// Initialize the plugin dependencies
	function init() {
		if ( $this->query('get_var', "SHOW TABLES LIKE '%table%'") == '' ) {
			$query_create = "CREATE TABLE %table% (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL,
				uri varchar(100) NOT NULL,
				title varchar(100) NOT NULL,
				count bigint(20) unsigned NOT NULL,
				UNIQUE KEY id (id)
				);";
			$this->query('query', $query_create);
		}
		add_option('dynamic_favorites_limit', 5);
	}

	// Update the favorites on admin page requests
	function update() {
		if ( stristr($_SERVER['SCRIPT_FILENAME'], '/wp-admin/index.php') && empty($_SERVER['QUERY_STRING']) )
			return;
		if ( stristr($_SERVER['REQUEST_URI'], 'media-upload.php') )
			return;
		global $title, $post, $current_user;
		$user_id = $current_user->ID;
		$args = array(	'action',
				'cat_ID',
				'link_id',
				'cat_id',
				'attachment_id',
				'detached',
				'post_mime_type',
				'tag_ID',
				'post',
				'post_status',
				'p',
				'author',
				'category_name',
				'comment_status',
				'c',
				's',
				'approved',
				'unapproved',
				'page',
				'user_id',
				'role'
				);
		$query_string = $_SERVER['QUERY_STRING'];
		$uri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
		foreach ( $args as $arg ) {
			if ( !empty($_GET[$arg]) ) {
				switch ($arg) {
					case 'author':
						$page_title = "$title - Author - " . $_GET[$arg];
						break;
					case 'detached':
						$page_title = "$title - unattached";
						break;
					case 'approved':
						$page_title = "$title - approved";
						break;
					case 'unapproved':
						$page_title = "$title - unapproved";
						break;
					case 'role':
						$page_title = "$title - Role - " . $_GET[$arg];
						break;
					case 'cat_id':
						$page_title = "$title - Cat - " . $_GET[$arg];
						break;
					case 'tag_id':
						$page_title = "$title - Tag - " . $_GET[$arg];
						break;
					case 'comment_status':
					case 'post_status':
					case 'post_mime_type':
						$page_title = "$title - " . ucfirst($_GET[$arg]);
						break;
					case 'page':
						$page_title = $title;
						break;
					default:
						$page_title = "$title - " . $_GET[$arg];	
						break;
				}
				if ( ($arg == 'action' && strstr($_GET[$arg],'edit')) || $arg != 'action' )
					$uri .= "&$arg=" . $_GET[$arg];
			}
		}
		if ( !isset($page_title) )
			$page_title = $title;
		if ( !strstr($uri, '?') && strstr($uri, '&') )
			$uri = preg_replace('/&/', '?', $uri, 1);
		$query_uri = "SELECT count FROM %table% WHERE user_id='$user_id' AND uri='$uri'";
		$count = $this->query('get_var', $query_uri);
		if ( isset($count) ) {
			$count++;
			$query_update = "UPDATE %table% SET count=$count WHERE user_id='$user_id' AND uri='$uri'";
			$this->query('query', $query_update);
		} else {
			$query_insert = "INSERT INTO %table% (user_id,uri,title,count) VALUES ('$user_id','$uri','$page_title',1)";
			$this->query('query', $query_insert);
		}
	}

	// Populate the favorites drop down with the recorded favorites
	function populate($favorites) {
		global $current_user;
		$user_id = $current_user->ID;
		if ( current_user_can('level_10') )
			$level = 'level_10';
		elseif ( current_user_can('level_7') )
			$level = 'level_7';
		elseif ( current_user_can('level_2') )
			$level = 'level_2';
		elseif ( current_user_can('level_1') )
			$level = 'level_1';
		elseif ( current_user_can('level_0') )
			$level = 'level_0';
		$limit = (int) get_option('dynamic_favorites_limit');
		$query_select = "SELECT uri,title FROM %table% WHERE user_id='$user_id' ORDER BY count DESC LIMIT $limit";
		$dynamic_favorites = $this->query('get_results', $query_select); 
		if ( count($dynamic_favorites) )
			$favorites = array();
		foreach ( $dynamic_favorites as $favorite ) {
			$uri = $favorite->uri;
			$title = $favorite->title;
			$favorites[$uri] = array($title, $level);
		}
		return $favorites;
	}

	// Delete favorites from table based on user_id
	function delete($user_id) {
		$query_delete = "DELETE FROM %table% WHERE user_id='$user_id'";
		$this->query('query', $query_delete);
	}

	// Database operations
	function query($type, $query) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->postfix;
		switch ($type) {
			case 'get_var':
				$result = $wpdb->get_var(str_replace('%table%', $table_name, $query));
				break;
			case 'get_results':
				$result = $wpdb->get_results(str_replace('%table%', $table_name, $query));
				break;
			case 'query':
				$result = $wpdb->query(str_replace('%table%', $table_name, $query));
				break;
		}
		return $result;
	}

	// Add the settings page
	function add_page() {
		if ( current_user_can('manage_options') && function_exists('add_options_page') ) :
			add_options_page('Dynamic Favorites', 'Dynamic Favorites', 'manage_options', 'dynamic-favorites', 'dynamic_favorites_admin_page');
		endif;
	}

	// The settings page
	function admin_page() {
		if ( isset($_POST['action']) && $_POST['action'] == 'update' ) {
			update_option('dynamic_favorites_limit',$_POST['dynamic_favorites_limit']);
			echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		}
	?>
	<div class="wrap">
		<h2>Dynamic Favorites</h2>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<input type="hidden" name="action" value="update" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
			   			Favorites Limit		     
					</th>
					<td>
						<input type="text" name="dynamic_favorites_limit" value="<?php echo get_option('dynamic_favorites_limit'); ?>" size="3" />
						<br />
						The number of items to show in the favorites drop down menu.  Default 5.
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
			</p>
		</form>
	</div>
	<?php
	}

	// Add option to personal options on user profile pages
	function user_edit() {
	?>
	<tr>
		<th><label for="dynamic_favorites_reset">Reset Dymanic Favorites</label></th>
		<td>
	
			<select name="dynamic_favorites_reset" id="dynamic_favorites_reset">
				<option value="false" selected="selected">false</option>
				<option value="true">true</option>
			</select>
			<br />
			Resets the list of your dynamically generated favorites.
			<tr>
		</td>
	</tr>
	<?php
	}

	// Get post on user updates
	function user_update() {
		if ( isset($_POST['dynamic_favorites_reset']) && $_POST['dynamic_favorites_reset'] == 'true' ) {
			global $wpdb, $user_id;
			$this->delete($user_id);
		}
	}
}

$dynamic_favorites = new dynamic_favorites();

// End if for is_admin
endif;
?>