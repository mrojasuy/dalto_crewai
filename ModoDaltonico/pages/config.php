<?php
require_api('authentication_api.php');
require_api('access_api.php');
require_api('config_api.php');
require_api('form_api.php');
require_api('gpc_api.php');
require_api('helper_api.php');
require_api('print_api.php');

auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

$t_error = '';
$t_success = '';

if (gpc_isset('submit')) {
	try {
		form_security_validate('plugin_ModoDaltonico_config');
		$f_enabled = gpc_get_bool('enabled', false);
		plugin_config_set('enabled', $f_enabled ? ON : OFF);
		form_security_purge('plugin_ModoDaltonico_config');
		$t_success = plugin_lang_get('messages_saved_success');
	} catch (Exception $e) {
		$t_error = plugin_lang_get('messages_csrf_error');
	}
}

$t_enabled = plugin_config_get('enabled', ON);

layout_page_header(plugin_lang_get('config_title'));
layout_page_begin();
?>
<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="widget-box widget-color-blue2">
		<div class="widget-header">
			<h4 class="widget-title lighter">
				<?php echo string_html_specialchars(plugin_lang_get('config_title')); ?>
			</h4>
		</div>
		<div class="widget-body">
			<div class="widget-main">
				<?php if ($t_error) { ?>
					<div class="alert alert-danger">
						<?php echo string_html_specialchars($t_error); ?>
					</div>
				<?php } elseif ($t_success) { ?>
					<div class="alert alert-success">
						<?php echo string_html_specialchars($t_success); ?>
					</div>
				<?php } ?>

				<form method="post" action="<?php echo string_attribute(plugin_page('config.php')); ?>" class="form-inline">
					<?php echo form_security_field('plugin_ModoDaltonico_config'); ?>
					<div class="table-responsive">
						<table class="table table-bordered table-condensed table-striped">
							<tr>
								<th class="width-30">
									<?php echo string_html_specialchars(plugin_lang_get('config_enabled_label')); ?>
								</th>
								<td>
									<label>
										<input type="checkbox" name="enabled" value="1" <?php echo $t_enabled == ON ? 'checked="checked"' : ''; ?>>
									</label>
									<div class="help-block">
										<?php echo string_html_specialchars(plugin_lang_get('config_enabled_help')); ?>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="space-10"></div>
					<input type="submit" name="submit" class="btn btn-primary btn-sm" value="<?php echo string_attribute(plugin_lang_get('save_button')); ?>">
				</form>

				<hr>
				<h5><?php echo string_html_specialchars(plugin_lang_get('plugin_title')); ?></h5>
				<p><?php echo string_html_specialchars(plugin_lang_get('ui_help_brief')); ?></p>
				<ul>
					<li><?php echo string_html_specialchars(plugin_lang_get('ui_toggle_on')); ?> / <?php echo string_html_specialchars(plugin_lang_get('ui_toggle_off')); ?></li>
					<li><?php echo string_html_specialchars(plugin_lang_get('ui_mode_label')); ?>: <?php echo string_html_specialchars(plugin_lang_get('ui_mode_protanopia')); ?>, <?php echo string_html_specialchars(plugin_lang_get('ui_mode_deuteranopia')); ?>, <?php echo string_html_specialchars(plugin_lang_get('ui_mode_tritanopia')); ?></li>
					<li><?php echo string_html_specialchars(plugin_lang_get('ui_indicator_active')); ?> / <?php echo string_html_specialchars(plugin_lang_get('ui_indicator_off')); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
layout_page_end();