<?php
/*
Plugin Name: Easy Mortgage Rates
Plugin URI: http://wordpress.org/extend/plugins/easy-mortgage-rates/
Description: Allows you to enter in various mortgage rate information into a table for display.
Version: .2
Author: Sheldon Chang
Author URI: http://www.hyperlinked.com
License: GPL2

Copyright 2011 Sheldon Chang  (email : sheldon@hyperlinked.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'setup_mortgage_rates');

function setup_mortgage_rates() {
	add_options_page('Change Mortgage Rates', 'Easy Mortgage Rates', 'manage_options', 'easy-mortgage-rates', 'update_mortgage_rates');
}

function _check_rate_format($rate) {
	$rate = trim($rate);
	if ($rate) {
		if ( !preg_match( "/^[0-9]{1,2}\.?[0-9]{1,4}$/", $rate ) ) {
		  die( '<div class="wrap"><h2>Form Entry Error</h2><p>You entered an invalid interest rate. Please go back and fix your error. Rates be entered in an "<strong>n.nn</strong>" format and cannot contain letters or symbols except for a period. If you do not wish to display a certain program, leave both fields for the omitted program blank.</p></div>' );
		}
		return $rate;
	}
}

function SaveMortgageRateData() {

	update_option('easy-mortgage-rates', array(
		array('30yrfixed',
			array('rate' => _check_rate_format($_POST['30yrfixed']['rate']),
				  'apr' => _check_rate_format($_POST['30yrfixed']['apr']),
				)
			),
		array('30yrhigh',
			array('rate' => _check_rate_format($_POST['30yrhigh']['rate']),
				  'apr' => _check_rate_format($_POST['30yrhigh']['apr']),
				)
			),
		array('15yrfixed',
			array('rate' => _check_rate_format($_POST['15yrfixed']['rate']),
				  'apr' => _check_rate_format($_POST['15yrfixed']['apr']),
				)
			),
		array('15yrhigh',
			array('rate' => _check_rate_format($_POST['15yrhigh']['rate']),
				  'apr' => _check_rate_format($_POST['15yrhigh']['apr']),
				)
			),
		array('51yrARM',
			array('rate' => _check_rate_format($_POST['51yrARM']['rate']),
				  'apr' => _check_rate_format($_POST['51yrARM']['apr']),
				)
			),
		array('71yrARM',
			array('rate' => _check_rate_format($_POST['71yrARM']['rate']),
				  'apr' => _check_rate_format($_POST['71yrARM']['apr']),
				)
			),
		array('101yrARM',
			array('rate' => _check_rate_format($_POST['101yrARM']['rate']),
				  'apr' => _check_rate_format($_POST['101yrARM']['apr']),
				)
			)
		)
	);

	if ( !preg_match( "/^[a-zA-Z]{0,2}$/", $_POST['state'] ) ) {
	  die( '<div class="wrap"><h2>Form Entry Error</h2>You used an invalid state code</div>' );
	}
	update_option('easy-mortgage-rates-state', $_POST['state']);

	$idle_interval = intval($_POST['idle_interval']) * 3600;
	update_option('easy-mortgage-rates-idle_interval', $idle_interval);
	update_option('easy-mortgage-rates-updated', time());
}

function update_mortgage_rates() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if (isset($_POST['Submit'])) {
		SaveMortgageRateData();
	}
	$options = get_option('easy-mortgage-rates');
	$time = time();

	$updatetime = get_option('easy-mortgage-rates-updated');
	$state = get_option('easy-mortgage-rates-state');
	$idle_interval = get_option('easy-mortgage-rates-idle_interval') / 3600;

	foreach ($options as $option) {
		$key = $option[0];
		$data = $option[1];
		foreach ($data as $k => $v) {
			$info[$key][$k] = $v;
		}
	}

	echo '<div class="wrap">';
	print <<<EOF
		<h2>Easy Mortgage Rates Interest Rates Table Settings</h2>
		<p>To use this widget, just add the template tag [easy_mortgage_rates_table] to your site. Your custom rates will be displayed unless the number of hours passes your set idle interval. Once a set number of hours has passed since
		the last manual rate update, your custom rates table will be replaced with the <a href="http://www.erate.com/widgets/rateWidget.html" target="_blank">ERATE.COM Mortgage Rates widget</a> with generic current rates.</p>
		<p>If you do not carry a loan program shown below, leave the corresponding rate and APR fields blank and the program will be omitted.</p>
		<p>If you do not wish to display APR information, leave all APR fields blank and no APR column will be shown.</p>
		<form method="post">
		<table cellspacing="0" summary="Mortgage Rates" class="form-table">
			<thead>
				<tr>
					<th scope="col" abbr="Program" class="left top">
						Program
					</th>
					<th scope="col" abbr="Rates" class="tdcenter top">
						Rates
					</th>
					<th scope="col" abbr="APR" class="tdcenter top">
						APR
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row" class="tick">
						<label>30 Year Fixed Conforming</label>
					</th>
					<td>
						<input type="text" name="30yrfixed[rate]" value="{$info['30yrfixed']['rate']}" />%
					</td>
					<td>
						<input type="text" name="30yrfixed[apr]" value="{$info['30yrfixed']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>30 Year High Balance</label>
					</th>
					<td>
						<input type="text" name="30yrhigh[rate]" value="{$info['30yrhigh']['rate']}" />%
					</td>
					<td>
						<input type="text" name="30yrhigh[apr]" value="{$info['30yrhigh']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>15 Year Fixed Conforming</label>
					</th>
					<td>
						<input type="text" name="15yrfixed[rate]" value="{$info['15yrfixed']['rate']}" />%
					</td>
					<td>
						<input type="text" name="15yrfixed[apr]" value="{$info['15yrfixed']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>15 Year High Balance</label>
					</th>
					<td>
						<input type="text" name="15yrhigh[rate]" value="{$info['15yrhigh']['rate']}" />%
					</td>
					<td>
						<input type="text" name="15yrhigh[apr]" value="{$info['15yrhigh']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>5/1 Year ARM Conforming</label>
					</th>
					<td>
						<input type="text" name="51yrARM[rate]" value="{$info['51yrARM']['rate']}" />%
					</td>
					<td>
						<input type="text" name="51yrARM[apr]" value="{$info['51yrARM']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>7/1 Year ARM Conforming</label>
					</th>
					<td>
						<input type="text" name="71yrARM[rate]" value="{$info['71yrARM']['rate']}" />%
					</td>
					<td>
						<input type="text" name="71yrARM[apr]" value="{$info['71yrARM']['apr']}" />%
					</td>
				</tr>
				<tr>
					<th scope="row" class="tick">
						<label>10/1 Year ARM Conforming</label>
					</th>
					<td>
						<input type="text" name="101yrARM[rate]" value="{$info['101yrARM']['rate']}" />%
					</td>
					<td>
						<input type="text" name="101yrARM[apr]" value="{$info['101yrARM']['apr']}" />%
					</td>
				</tr>
				<tr valign="top">
					<th><label>State</label></th>
					<td colspan="2">
						<input type="text" name="state" value="{$state}"> <span class="description">Two letter abbreviation of the state whose rates you want to display. Leave blank for national average rates.</span>
					</td>
				</tr>
				<tr valign="top">
					<th><label>Idle Interval</label></th>
					<td colspan="2">
						<input type="text" name="idle_interval" value="{$idle_interval}"> <span class="description">Number of hours that need to pass before the erate.com widget is used instead of your custom rates.</span>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<p class="submit" style="text-align:center;">
		<input type="submit" name="Submit" value="Submit" />
		</p>
		</form>
EOF;
	echo '</div>';

}

function get_erate_widget() {

	$state = get_option('easy-mortgage-rates-state');
	$url = "http://www.erate.com/widgets/getRates.php?state={$state}";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$data 	= curl_exec($ch);
	$status = curl_getinfo($ch);

	curl_close($ch);

	if ($status['http_code']=="200")
	{
		if ($data)
		{
			return "<script>{$data}</script>";
			exit;
		}
	}
}

/* Function: easy_mortgage_rates_table
	** Outputs rates table
	**
	** returns: nothing
*/


function easy_mortgage_rates_table() {

	$programs = array(
		'30yrfixed'	=> __('30 Year Fixed Conforming'),
		'30yrhigh'	=> __('30 Year High Balance'),
		'15yrfixed'	=> __('15 Year Fixed Conforming'),
		'15yrhigh'	=> __('15 Year High Balance'),
		'51yrARM'	=> __('5/1 Year ARM Conforming'),
		'71yrARM'	=> __('7/1 Year ARM Conforming'),
		'101yrARM'	=> __('10/1 Year ARM Conforming'),
	);

	$updated = get_option('easy-mortgage-rates-updated');
	$time_since_update = time() - $updated;
	$idle_interval = get_option('easy-mortgage-rates-idle_interval');

	$update_date = date('m/d/Y', get_option('easy-mortgage-rates-updated'));


	if ($time_since_update > $idle_interval) {
		$widget = get_erate_widget();
		return $widget;
	}

	$options = get_option('easy-mortgage-rates');

	foreach ($options as $option) {
		$key = $option[0];
		$data = $option[1];
		foreach ($data as $k => $v) {
			if ($v > 0) {
				$info[$key][$k] = $v;
			}
		}
	}

	foreach ($info as $key => $rates) {
		if ($rates['rate']) {
			$row = '<td>'.$rates['rate'].'%</td>';
			$rate_set = 1;
		}
		if ($rates['apr']) {
			$row .= '<td>'.$rates['apr'].'%</td>';
			$apr_set = 1;
		}

		if ($row) {
			$rows .= <<<EOF
			<tr>
				<td scope="row" class="tick">{$programs[$key]}</td>
				{$row}
			</tr>
EOF;
		}
		unset($row);
	}

	$cols = 1;

	$thead = '<tr><th scope="col" abbr="Program" class="left top">Program</th>';

	if (isset($rate_set)) {
		$thead .= '<th scope="col" abbr="Rates" class="tdcenter top">Rates</th>';
		$cols++;
	}

	if (isset($apr_set)) {
		$thead .= '<th scope="col" abbr="APR" class="tdcenter top">APR</th>';
		$cols++;
	}
	$thead .= '</tr>';

	$table = <<<EOF
		<table cellspacing="0" summary="Mortgage Rates" class="ratesTable">
		<thead>
			{$thead}
		</thead>
		<tbody>
			{$rows}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="{$cols}" class="left lateUpdated"><p>Last Updated: {$update_date}</p></td>
				<td></td>
			</tr>
		</tfoot>
		</table>

EOF;
	return $table;
}


function widget_display_rates_table() {
	$title = get_option('easy_mortage_rates_title');
	print '<h3 id="easy_mortgage_title">'.$title.'</h3> '.easy_mortgage_rates_table();
}

function widget_display_rates_table_control() {
	$title = get_option('easy_mortage_rates_title');
  print <<<EOF
  	<p><label>Title<input name="easy_mortage_rates_title" type="text" value="{$title}" /></label></p>
EOF;

	if (isset($_POST['easy_mortage_rates_title'])){
    	$data = attribute_escape($_POST['easy_mortage_rates_title']);
    	update_option('easy_mortage_rates_title', $data);
  	}
}

function easy_mortgage_rates_css() {
		$blog_url = get_bloginfo('wpurl');
		print <<<EOF
			<link rel="stylesheet" type="text/css" href="{$blog_url}/wp-content/plugins/easy-mortgage-rates/easy-mortgage-rates.css" />
EOF;
}

function easyMortgageRates_init()
{
	add_shortcode('easy_mortgage_rates_table', 'easy_mortgage_rates_table');
	add_action('wp_head', 'easy_mortgage_rates_css');
	wp_register_sidebar_widget('easy_mortage_rates_table', __('Display Rates Table'), 'widget_display_rates_table');
	wp_register_widget_control('easy_mortage_rates_table', __('Display Rates Table'), 'widget_display_rates_table_control');
}
add_action("plugins_loaded", "easyMortgageRates_init");


?>