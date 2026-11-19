<?php
class ModoDaltonicoPlugin extends MantisPlugin {
	public function register() : void {
		$this->name = 'Modo Daltónico';
		$this->description = 'Accesibilidad: Modo daltónico con filtros visuales y selector de tipo.';
		$this->version = '1.0.0';
		$this->requires = array('MantisCore' => '2.0.0');
		$this->author = 'Equipo de Plugins';
		$this->contact = 'plugins@example.com';
		$this->url = 'https://example.com/mantisbt/modo-daltonico';
		$this->page = 'config.php';
	}

	public function config() : array {
		return array(
			'enabled' => ON,
		);
	}

	public function events() : array {
		return array();
	}

	public function hooks() : array {
		return array(
			EVENT_MENU_MAIN => 'menu_main',
			EVENT_LAYOUT_RESOURCES => 'layout_resources',
			EVENT_LAYOUT_BODY_END => 'layout_body_end',
		);
	}

	public function menu_main($p_event, $p_params = null) : array {
		if (!access_has_global_level(config_get('manage_plugin_threshold'))) {
			return array();
		}
		$t_label = plugin_lang_get('menu_title');
		$t_url = plugin_page('config.php');
		$t_link = '<a class="btn btn-xs btn-primary" href="' . string_attribute($t_url) . '">' . string_html_specialchars($t_label) . '</a>';
		return array($t_link);
	}

	public function layout_resources($p_event) : string {
		if (OFF == plugin_config_get('enabled')) {
			return '';
		}
		$t_css = '<link rel="stylesheet" type="text/css" href="' . string_attribute(plugin_file('style.css')) . '" />';
		$t_js = '<script src="' . string_attribute(plugin_file('script.js')) . '"></script>';
		return $t_css . "\n" . $t_js . "\n";
	}

	public function layout_body_end($p_event) : string {
		if (OFF == plugin_config_get('enabled')) {
			return '';
		}

		$t_toggle_on = plugin_lang_get('ui_toggle_on');
		$t_toggle_off = plugin_lang_get('ui_toggle_off');
		$t_mode_label = plugin_lang_get('ui_mode_label');
		$t_protanopia = plugin_lang_get('ui_mode_protanopia');
		$t_deuteranopia = plugin_lang_get('ui_mode_deuteranopia');
		$t_tritanopia = plugin_lang_get('ui_mode_tritanopia');
		$t_indicator_active = plugin_lang_get('ui_indicator_active');
		$t_indicator_off = plugin_lang_get('ui_indicator_off');
		$t_help_brief = plugin_lang_get('ui_help_brief');

		$out  = '<div id="mdal-config" hidden="hidden"';
		$out .= ' data-plugin-enabled="1"';
		$out .= ' data-lang-toggle-on="' . string_attribute($t_toggle_on) . '"';
		$out .= ' data-lang-toggle-off="' . string_attribute($t_toggle_off) . '"';
		$out .= ' data-lang-mode-label="' . string_attribute($t_mode_label) . '"';
		$out .= ' data-lang-protanopia="' . string_attribute($t_protanopia) . '"';
		$out .= ' data-lang-deuteranopia="' . string_attribute($t_deuteranopia) . '"';
		$out .= ' data-lang-tritanopia="' . string_attribute($t_tritanopia) . '"';
		$out .= ' data-lang-indicator-active="' . string_attribute($t_indicator_active) . '"';
		$out .= ' data-lang-indicator-off="' . string_attribute($t_indicator_off) . '"';
		$out .= ' data-lang-help-brief="' . string_attribute($t_help_brief) . '"';
		$out .= '></div>';

		return $out;
	}
}