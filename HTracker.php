<?php

class HTracker {

	private $user;

	const
		C_NAME = 'Health Tracker',
		C_SLUG = 'health-tracker';

	public function __construct() {
		global $wpdb;

		add_action('admin_menu', array($this, 'ht_admin_options'));
		add_action('bp_init', array($this, 'init'), 11);

		$this->user = bp_loggedin_user_id();

		$wpdb->health_questions	= $wpdb->prefix . 'health_questions';
		$wpdb->health_data		= $wpdb->prefix . 'health_data';
		$wpdb->health_routine	= $wpdb->prefix . 'health_routine';
		$wpdb->health_settings	= $wpdb->prefix . 'health_settings';
	}

	public function init() {
		global $bp;

		$this->user = (bp_displayed_user_id()) ? bp_displayed_user_id() : bp_loggedin_user_id();

		if(!$this->ht_get_meta_value('tracker_status')) {
			return;
		}

		if($this->ht_get_meta_value('tracker_status') && $this->ht_get_meta_value('tracker_status') == 0) {
			return;
		}
		
		$parent_url = trailingslashit($bp->displayed_user->domain . self::C_SLUG);

		$panel = array(
            'name' => __(self::C_NAME, 'buddypress'),
            'slug' => self::C_SLUG,
            'position' => 20,
            'show_for_displayed_user' => true,
            'screen_function' => array($this, 'loadTemplate'),
            'default_subnav_slug' => self::C_SLUG
        );
        bp_core_new_nav_item($panel);

        $newEntry = array(
            'name'            => 'New Entry',
            'slug'            => 'new-entry',
            'parent_url'      => $parent_url,
            'parent_slug'     => self::C_SLUG,
            'screen_function' => array($this, 'loadTemplate'),
            'position'        => 20,
            'user_has_access' => (bp_is_my_profile() || current_user_can('administrator'))
        );
        bp_core_new_subnav_item($newEntry);

        $routine = array(
            'name'            => 'Variables',
            'slug'            => 'variables',
            'parent_url'      => $parent_url,
            'parent_slug'     => self::C_SLUG,
            'screen_function' => array($this, 'loadTemplate'),
            'position'        => 20,
            'user_has_access' => (bp_is_my_profile() || current_user_can('administrator'))
        );
        bp_core_new_subnav_item($routine);

        add_action('bp_template_content', array($this, 'loadUI'));
	}

	public function loadTemplate() {
		bp_core_load_template('members/single/plugins');
	}

	public function loadUI() {
		global $bp;

		if(isset($bp->bp_options_nav[self::C_SLUG])) {
			foreach($bp->bp_options_nav[self::C_SLUG] as $action) {
				if($bp->current_action == $action['slug']) {
					include('view/ht-' . $bp->current_action . '.php');
					break;
				} elseif($bp->current_action == self::C_SLUG) {
					include('view/ht-new-entry.php');
					break;
				}
			}
		}
	}

	public function notice() {
		echo '<div class="error">
				<p>Health Tracker requires BuddyPress to run. Please install and activate BuddyPress first.</p>
			</div>';
	}

	public function checkUser() {
		if(!$this->user) {
			return;
		}
	}

	public function ht_admin_options() {
		add_options_page("Health Tracker", "Health Tracker", 'activate_plugins', "health-tracker", array($this, "ht_admin"));
	}

	public function ht_admin() {
		include('admin/panel.php');
	}

	public function ht_routine_add($name) {
		global $wpdb;

		$this->checkUser();

		$q = "INSERT INTO $wpdb->health_routine (`user_id`, `name`, `active`) VALUES($this->user, '" . esc_sql($name) . "', 1)";
		$wpdb->query($q);
	}

	public function ht_routine_edit($routine_id, $name) {
		global $wpdb;

		$this->checkUser();

		$q = "UPDATE $wpdb->health_routine SET name = '" . esc_sql($name) . "' WHERE user_id = $this->user AND id = " . esc_sql($routine_id);
		$wpdb->query($q);
	}

	public function ht_routine_delete($routine_id) {
		global $wpdb;

		$this->checkUser();

		$q = "UPDATE $wpdb->health_routine SET active = 0 WHERE id = " . esc_sql($routine_id);
		$wpdb->query($q);
	}

	public function ht_routine_get_all() {
		global $wpdb;

		$this->checkUser();

		return $wpdb->get_results("SELECT id, name FROM $wpdb->health_routine WHERE user_id = $this->user AND active = 1");

	}

	public function ht_question_answer($unq, $q_id, $r_id, $answer) {
		global $wpdb;

		$this->checkUser();

		$q = "INSERT INTO $wpdb->health_data (`unq`, `q_id`, `r_id`, `user_id`, `answer`) 
				VALUES('" . $unq . "', $q_id, $r_id, $this->user, '" . esc_sql($answer) . "')";
		$wpdb->query($q);
	}

	public function ht_question_add($question, $answers = array(), $type, $multiple, $status, $color) {
		global $wpdb;

		$color = substr($color, 0, 6);
		$q = "INSERT INTO $wpdb->health_questions (`question`, `answers`, `type`, `multiple`, `status`, `active`, `color`) 
				VALUES('" . esc_sql($question) . "', '" . serialize($answers) ."', $type, " . esc_sql($multiple) . ", " . esc_sql($status) . ", 1, '" . esc_sql($color) ."')";
		$wpdb->query($q);
	}

	public function ht_question_delete($q_id) {
		global $wpdb;

		$q = "UPDATE $wpdb->health_questions SET active = 0 WHERE id = $q_id";
		$wpdb->query($q);
	}

	public function ht_question_update($q_id, $status, $type, $multiple) {
		global $wpdb;

		$q = "UPDATE $wpdb->health_questions SET status = " . esc_sql($status) . ", type = " . esc_sql($type) . ", multiple = " . esc_sql($multiple) ." WHERE id = " . esc_sql($q_id);
		$wpdb->query($q);
	}

	public function ht_question_get_all($byStatus = false) {
		global $wpdb;

		if($byStatus) {
			$result = $wpdb->get_results("SELECT * FROM $wpdb->health_questions WHERE active = 1 AND status = 1");
		} else {
			$result = $wpdb->get_results("SELECT * FROM $wpdb->health_questions WHERE active = 1");
		}
		return $result;
	}

	public function ht_question_get_one($q_id) {
		global $wpdb;

		return $wpdb->get_row("SELECT * FROM $wpdb->health_questions WHERE id = " . esc_sql($q_id));
	}


	public function ht_get_answers($r_id = null, $q_type, $start = null, $end = null) {
		global $wpdb;

		$this->checkUser();

		if(!$r_id) {
			$r_id = $wpdb->get_row("SELECT id FROM $wpdb->health_routine WHERE user_id = $this->user AND active = 1 LIMIT 1");
			$r_id = (int) $r_id->id;
		}

		if($start && $end) {
			$result = $wpdb->get_results("SELECT q_id, answer, created FROM $wpdb->health_data 
						WHERE q_id IN (SELECT id FROM $wpdb->health_questions WHERE type = " . esc_sql($q_type) . ") 
						AND r_id = " . esc_sql($r_id) . " 
						AND user_id = $this->user 
						AND created >= '" . esc_sql($start) . "' AND created <='" . esc_sql($end) . "'", OBJECT);
		} else {
			$result = $wpdb->get_results("SELECT q_id, answer, created FROM $wpdb->health_data 
						WHERE q_id IN (SELECT id FROM $wpdb->health_questions WHERE type = " . esc_sql($q_type) . ") 
						AND user_id = $this->user 
						AND r_id = " . esc_sql($r_id), OBJECT);
		}

		return $result;
	}

	public function ht_get_all_answers() {
		global $wpdb;

		$this->checkUser();

		$result = $wpdb->get_results("SELECT q.question, c.name, d.answer, d.created, d.unq FROM $wpdb->health_data d
						INNER JOIN $wpdb->health_questions q ON (d.q_id = q.id)
						INNER JOIN $wpdb->health_routine c ON (d.r_id = c.id)
						WHERE d.user_id = $this->user", OBJECT);

		return $result;
	}

	public function ht_add_meta($name, $value) {
		global $wpdb;

		$meta = $wpdb->get_row("SELECT meta_name FROM $wpdb->health_settings WHERE meta_name = '" . esc_sql($name) . "'", OBJECT);

		if($meta->meta_name) {
			$q = "UPDATE $wpdb->health_settings SET value = '" . esc_sql($value) . "' WHERE meta_name = '" . esc_sql($name) . "'";
			$wpdb->query($q);
		} else {
			$q = "INSERT INTO $wpdb->health_settings (`meta_name`, `value`) VALUES('" . esc_sql($name) . "','" . esc_sql($value) . "')";
			$wpdb->query($q);
		}	
	}

	public function ht_get_meta_value($name) {
		global $wpdb;

		$meta = $wpdb->get_row("SELECT value FROM {$wpdb->health_settings} WHERE meta_name = '" . esc_sql($name) . "'", OBJECT);

		return $meta->value;
	}

	public function ht_migrate() {
		global $wpdb;

		$charset_collate 	= $wpdb->get_charset_collate();

		$sql_q = "CREATE TABLE IF NOT EXISTS `" . $wpdb->health_questions . "` (
			`id` int(20) NOT NULL AUTO_INCREMENT,
			`question` text NOT NULL,
			`answers` varchar(512) NOT NULL,
			`type` tinyint(1) NOT NULL,
			`color` varchar(6) NOT NULL,
			`multiple` tinyint(1) NOT NULL,
			`status` tinyint(1) NOT NULL,
			`active` tinyint(1)	NOT NULL,
			UNIQUE KEY `id` (`id`)
			) $charset_collate;";

		$sql_d = "CREATE TABLE IF NOT EXISTS `" . $wpdb->health_data . "` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`q_id` int(20) NOT NULL,
			`r_id` int(20) NOT NULL,
			`unq` varchar(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
			`answer` varchar(100) NOT NULL,
			`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY `id` (`id`)
			) $charset_collate;";

		$sql_r = "CREATE TABLE IF NOT EXISTS `" . $wpdb->health_routine . "` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`name` varchar(150) NOT NULL,
			`active` tinyint(1) NOT NULL,
			UNIQUE KEY `id` (`id`)
			) $charset_collate;";

		$sql_s = "CREATE TABLE IF NOT EXISTS `" . $wpdb->health_settings . "` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`meta_name` varchar(50) NOT NULL,
			`value` tinyint(1) NOT NULL,
			UNIQUE KEY `id` (`id`)
			) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$wpdb->query($sql_q);
		$wpdb->query($sql_d);
		$wpdb->query($sql_r);
		$wpdb->query($sql_s);
	}

	// Helpers

	public function hex2rgba($color, $opacity = false) {

		$default = 'rgb(0,0,0)';

		if(empty($color))
			return $default;

	    if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
	    }

	    if (strlen($color) == 6) {
	            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	    } elseif ( strlen( $color ) == 3 ) {
	            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	    } else {
	            return $default;
	    }

	    $rgb =  array_map('hexdec', $hex);

	    if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;

			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	    } else {
			$output = 'rgb('.implode(",",$rgb).')';
	    }

	    return $output;
	}

	public function dump($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}