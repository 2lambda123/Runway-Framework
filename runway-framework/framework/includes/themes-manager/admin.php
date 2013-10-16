	<?php

global $developer_tools, $Themes_Manager_Admin, $extm;
$extensions_dir = TEMPLATEPATH . '/framework/extensions/';

$required = '<em class="required">' . __( 'Required', THEME_NAME ) . '</em>';
$_data = $Themes_Manager_Admin->data;

$themes_path = explode( '/', TEMPLATEPATH );
unset( $themes_path[count( $themes_path ) - 1] );
$themes_path = implode( '/', $themes_path );

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($action) {
	case 'delete-package':{
		$package = isset($_REQUEST['package']) ? $_REQUEST['package'] : '';
		$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
		if($name != '' && $package != ''){
			$alone_theme_file = "$name-($package).a.zip";
			$child_theme_file = "$name-($package).c.zip";
			$download_dir = $developer_tools->themes_path."/$name/download/";
			if(unlink($download_dir.$alone_theme_file)){
				// out message
			}

			if(unlink($download_dir.$child_theme_file)){
				// out message
			}
		}
	} break;
}

switch ( $this->navigation ) {
	case 'do-package': {

		if ( isset( $_REQUEST['name'] ) ) {
			require_once 'views/download.php';
		} else {
			echo 'oops...';
		}
	} break;

	case 'do-download': {
		$theme_settings = $developer_tools->load_settings( $_REQUEST['name'] );
		$history = $theme_settings['History'];

		require_once 'views/download.php';
	} break;

	case 'duplicate-theme': {
		/* under construction */
		if ( isset( $_REQUEST['name'] ) && isset( $_REQUEST['new_name'] ) ) {
			if ( isset( $_REQUEST['save'] ) ) {
				$post = stripslashes_deep( $_POST['theme_options'] );
				$errors = $developer_tools->validate_theme_settings( $post );
				if ( count( $errors ) ) {
					$options = $post;
					require_once 'views/theme-conf.php';
				} else {
					$developer_tools->build_and_save_theme( $post );
					require_once 'views/themes-list.php';
				}
			} else {
				$options = $developer_tools->make_theme_copy( $_REQUEST['name'], $_REQUEST['new_name'] );
				require_once 'views/theme-conf.php';
			}
		}
	} break;

	case 'edit-theme': {
		$developer_tools->mode = 'edit';

		if ( isset( $_REQUEST['save'] ) ) {
			$post = stripslashes_deep( $_POST['theme_options'] );
			$errors = $developer_tools->validate_theme_settings( $post );
			if ( count( $errors ) ) {
				$options = $post;
				require_once 'views/theme-conf.php';
			} else {
				$options = $developer_tools->build_and_save_theme( $post, false );				
				
				if($post['Folder'] != $post['old_folder_name']){
					update_option('stylesheet', $post['Folder']);
				}
				
				$ts = time();
				$history = $developer_tools->get_history( $options['Folder'] );				
				$alone_package_download_url = $developer_tools->build_alone_theme( $options['Folder'], $ts );
				$child_package_download_url = $developer_tools->build_child_package( $options['Folder'], $ts );
				$developer_tools->make_package_info_from_ts( $options['Folder'], $ts );
				require_once 'views/themes-list.php';				
			}
		} else {
			require_once 'views/theme-conf.php';
		}
	} break;

	case 'delete-theme': {

		if ( isset( $_REQUEST['confirm'] ) ) {
			if ( isset( $_REQUEST['name'] ) && $_REQUEST['name'] != 'runway' ) {
				$developer_tools->delete_child_theme( $_REQUEST['name'] );
			}

			require_once 'views/themes-list.php';
		}
		else {
			if ( isset( $_REQUEST['name'] ) ) {
				$del_theme_info = rw_get_theme_data( $themes_path.'/'.$_REQUEST['name'] );
				include_once 'views/del-theme-confirmation.php';
			}
		}
	} break;

	case 'new-theme': {
		$developer_tools->mode = 'new';

		if ( isset( $_POST['theme_options'] ) ) {
			$post = stripslashes_deep( $_POST['theme_options'] );
			$errors = $developer_tools->validate_theme_settings( $post );
			if ( count( $errors ) ) {
				$options = $post;
				require_once 'views/theme-conf.php';
			} else {
				$options = $developer_tools->build_and_save_theme( $post );
				require_once 'views/themes-list.php';
			}
		} else {
			require_once 'views/theme-conf.php';
		}
	} break;

	case 'list-runway-themes': { }

	case 'confirm-del-package':{		
		$name = $_REQUEST['name'];
		$package = isset($_REQUEST['package']) ? $_REQUEST['package'] : '';		
		$alone_theme_file = "$name-($package).a.zip";
		$child_theme_file = "$name-($package).c.zip";
		$package_info = $developer_tools->make_package_info_from_ts( $name, $package );
		include_once 'views/del-package-confirmation.php';
	} break;

	default: {
		require_once 'views/themes-list.php';
	} break;
}
?>
