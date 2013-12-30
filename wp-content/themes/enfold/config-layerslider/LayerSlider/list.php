<?php

	// Get WPDB Object
	global $wpdb;

	// Table name
	$table_name = $wpdb->prefix . "layerslider";

	// Get sliders
	$sliders = lsSliders(200, false, true);

	// Custom capability
	$custom_capability = $custom_role = get_option('layerslider_custom_capability', 'manage_options');
	$default_capabilities = array('manage_network', 'manage_options', 'publish_pages', 'publish_posts', 'edit_posts');

	if(in_array($custom_capability, $default_capabilities)) {
		$custom_capability = '';
		$custom_role_placeholder = "Select 'Custom' to enter a capability";
	} else {
		$custom_role = 'custom';
	}

	// Auto-updates
	$code = get_option('layerslider-purchase-code', '');
	$validity = get_option('layerslider-validated', '0');
	$channel = get_option('layerslider-release-channel', 'stable');


	// Get screen options
	$lsScreenOptions = get_option('lsScreenOptions', '0');
	$lsScreenOptions = ($lsScreenOptions == 0) ? array() : $lsScreenOptions;
	$lsScreenOptions = is_array($lsScreenOptions) ? $lsScreenOptions : unserialize($lsScreenOptions);

	// Defaults
	if(!isset($lsScreenOptions['showTooltips'])) {
		$lsScreenOptions['showTooltips'] = 'true';
	}

?>

<div id="ls-screen-options" class="metabox-prefs hidden">
	<div id="screen-options-wrap" class="hidden">
		<form id="ls-screen-options-form" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
			<h5>Show on screen</h5>
			<label>
				<input type="checkbox" name="showTooltips"<?php echo $lsScreenOptions['showTooltips'] == 'true' ? ' checked="checked"' : ''?>> Tooltips
			</label>
		</form>
	</div>
	<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
		<a href="#screen-options-wrap" id="show-settings-link" class="show-settings">Screen Options</a>
	</div>
</div>

<?php if(isset($_GET['message'])) : ?>
<div class="<?php echo isset($_GET['error']) ? 'error' : 'updated' ?>"><?php echo $_GET['message'] ?></div>
<?php endif; ?>

<div class="wrap">
	<div class="ls-icon-layers"></div>
	<h2>
		<?php _e('LayerSlider sliders', 'LayerSlider') ?>
		<a href="?page=layerslider_add_new" class="add-new-h2"><?php _e('Add New', 'LayerSlider') ?></a>
		<a href="?page=layerslider&action=import_sample" class="add-new-h2"><?php _e('Import sample sliders', 'LayerSlider') ?></a>
	</h2>

	<div class="ls-box ls-slider-list">
		<table>
			<thead>
				<tr>
					<td>ID</td>
					<td><?php _e('Name', 'LayerSlider') ?></td>
					<td><?php _e('Shortcode', 'LayerSlider') ?></td>
					<td><?php _e('Actions', 'LayerSlider') ?></td>
					<td><?php _e('Created', 'LayerSlider') ?></td>
					<td><?php _e('Modified', 'LayerSlider') ?></td>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($sliders)) : ?>
				<?php foreach($sliders as $key => $item) : ?>
				<?php $name = empty($item['name']) ? 'Unnamed' : $item['name']; ?>
				<tr>
					<td><?php echo $item['id'] ?></td>
					<td><a href="?page=layerslider&action=edit&id=<?php echo $item['id'] ?>"><?php echo $name ?></a></td>
					<td>[layerslider id="<?php echo $item['id'] ?>"]</td>
					<td>
						<a href="?page=layerslider&action=edit&id=<?php echo $item['id'] ?>"><?php _e('Edit', 'LayerSlider') ?></a> |
						<a href="?page=layerslider&action=duplicate&id=<?php echo $item['id'] ?>"><?php _e('Duplicate', 'LayerSlider') ?></a> |
						<a href="?page=layerslider&action=remove&id=<?php echo $item['id'] ?>" class="remove"><?php _e('Remove', 'LayerSlider') ?></a>
					</td>
					<td><?php echo date('M. d. Y.', $item['date_c']) ?></td>
					<td><?php echo date('M. d. Y.', $item['date_m']) ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if(empty($sliders)) : ?>
				<tr>
					<td colspan="6"><?php _e("You didn't create a slider yet.", "LayerSlider") ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php if($GLOBALS['lsAutoUpdateBox'] == true) : ?>
	<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="ls-box ls-settings ls-auto-update">
		<input type="hidden" name="action" value="layerslider_verify_purchase_code">
		<h3 class="header"><?php _e('Auto-updates', 'LayerSlider') ?></h3>
		<table>
			<tbody>
				<tr>
					<td>
						<?php _e('Purchase code:', 'LayerSlider') ?>
						<input type="texT" name="purchase_code" value="<?php echo $code ?>"  class="key" placeholder="bc8e2b24-3f8c-4b21-8b4b-90d57a38e3c7" data-help="<?php _e('To receive auto-updates, you need to enter your item purchase code. You can find it on your CodeCanyon downloads page, just click on the Download button and choose the "Licence Certificate" option. This will download a text file that contains your purchase code.', 'LayerSlider') ?>">
						<?php _e('Release channel:', 'LayerSlider') ?>
						<label><input type="radio" name="channel" value="stable" <?php echo ($channel === 'stable') ? 'checked="checked"' : ''?>> <?php _e('Stable', 'LayerSlider') ?></label>
						<label data-help="<?php _e('Although pre-release versions should be fine, they might contain unknown issues, and are not recommended for sites in production.', 'LayerSlider') ?>">
							<input type="radio" name="channel" value="beta" <?php echo ($channel === 'beta') ? 'checked="checked"' : ''?>> <?php _e('Beta', 'LayerSlider') ?>
						</label>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<button class="button"><?php _e('Save changes', 'LayerSlider') ?></button>
						<span style="<?php echo ($validity == '0' && $code != '') ? 'color: #c33219;' : 'color: #4b982f'?>">
							<?php
								if($validity == '1') {
									_e('Thank you for purchasing LayerSlider WP. You successfully validated your purchase code for auto-updates.', 'LayerSlider');
								} else if($code != '') {
									_e("Your purchase code doesn't appear to be valid. Please make sure that you entered your purchase code correctly.", "LayerSlider");
								}
							?>
						</span>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
	<?php endif; ?>

	<div class="columns clearfix">
		<div class="half">
			<div class="ls-import-export-box ls-box">
				<h3 class="header medium"><?php _e('Import & Export Sliders', 'LayerSlider') ?></h3>
				<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" enctype="multipart/form-data" class="ls-import-box">
					<input type="hidden" name="ls-import" value="1">
					<table data-help="<?php _e('Choose a LayerSlider export file downloaded previously to import your sliders. To import from older versions, you need to create a file and paste the export code into it. Only the file contents matters, its name does not.', 'LayerSlider') ?>">
						<tbody>
							<tr>
								<td><?php  _e('Import Sliders', 'LayerSlider') ?></td>
								<td><input type="file" name="import_file"></td>
								<td><button class="button"><?php _e('Import', 'LayerSlider') ?></button></td>
							</tr>
						</tbody>
					</table>
				</form>
				<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
					<input type="hidden" name="ls-export" value="1">
					<table data-help="<?php _e('Downloads an export file that contains your selected sliders to import on your new site.', 'LayerSlider') ?>">
						<tbody>
							<tr>
								<td><?php _e('Export Sliders', 'LayerSlider') ?></td>
								<td>
									<select name="sliders">
										<option value="-1">All Sliders</option>
										<?php foreach($sliders as $slider) : ?>
										<option value="<?php echo $slider['id'] ?>">
											#<?php echo str_replace(' ', '&nbsp;', str_pad($slider['id'], 3, " ")) ?> -
											<?php echo apply_filters('ls_slider_title', $slider['name'], 25) ?>
										</option>
										<?php endforeach; ?>
									</select>
								</td>
								<td><button class="button"><?php _e('Export', 'LayerSlider') ?></button></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>

		<div class="half">
			<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="ls-box ls-import-export-box" id="ls-permission-form">
				<input type="hidden" name="ls-access-permission" value="1">
				<h3 class="header medium"><?php _e('Allow LayerSlider access to users with ...', 'LayerSlider') ?></h3>
				<table>
					<tbody>
						<tr>
							<td><?php _e('Role', 'LayerSlider') ?></td>
							<td>
								<select name="custom_role">
									<?php if(is_multisite()) : ?>
									<option value="manage_network" <?php echo ($custom_role == 'manage_network') ? 'selected="selected"' : '' ?>>Super Admin</option>
									<?php endif; ?>
									<option value="manage_options" <?php echo ($custom_role == 'manage_options') ? 'selected="selected"' : '' ?>>Admin</option>
									<option value="publish_pages" <?php echo ($custom_role == 'publish_pages') ? 'selected="selected"' : '' ?>>Editor</option>
									<option value="publish_posts" <?php echo ($custom_role == 'publish_posts') ? 'selected="selected"' : '' ?>>Author</option>
									<option value="edit_posts" <?php echo ($custom_role == 'edit_posts') ? 'selected="selected"' : '' ?>>Contributor</option>
									<option value="custom" <?php echo ($custom_role == 'custom') ? 'selected="selected"' : '' ?>>Custom</option>
								</select>
							</td>
							<td><button class="button"><?php _e('Update', 'LayerSlider') ?></button></td>
							<!-- <td class="desc"><?php _e('If you want to give access for other users than admins to this page, you can specify a custom capability. You can find all the available capabilities on', 'LayerSlider') ?> <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capabilities" target="_blank"><?php _e('this page', 'LayerSlider') ?></a>.</td> -->
						</tr>
						<tr>
							<td><?php _e('Capability', 'LayerSlider') ?></td>
							<td><input type="text" name="custom_capability" value="<?php echo $custom_capability ?>" placeholder="<?php echo $custom_role_placeholder ?>"></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<div class="ls-box ls-news">
		<h2 class="header">LayerSlider News</h2>
		<div>
			<iframe src="http://news.kreaturamedia.com/layerslider/"></iframe>
		</div>
	</div>
</div>

<!-- Help menu WP Pointer -->
<?php

// Get users data
global $current_user;
get_currentuserinfo();

if(get_user_meta($current_user->ID, 'layerslider_help_wp_pointer', true) != '1') {
add_user_meta($current_user->ID, 'layerslider_help_wp_pointer', '1'); ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#contextual-help-link-wrap').pointer({
			pointerClass : 'ls-help-pointer',
			pointerWidth : 320,
			content: '<h3><?php _e('The documentation is here', 'LayerSlider') ?></h3><div class="inner"><?php _e('This is a WordPress contextual help menu, we use it to give you fast access to our documentation. Please keep in mind that because this menu is contextual, it only shows the relevant information to the page that you are currently viewing. So if you search something, you should visit the corresponding page first and then open this help menu.', 'LayerSlider') ?></div>',
			position: {
				edge : 'top',
				align : 'right'
			}
		}).pointer('open');
	});
</script>
<?php } ?>


<script type="text/javascript">
	// Screen options
	var lsScreenOptions = <?php echo json_encode($lsScreenOptions) ?>;
</script>
