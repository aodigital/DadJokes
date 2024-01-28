<?php
/**
 * Plugin Name: Dad Jokes Generator
 * Plugin URI: https://aodigital.com.au/wordpress-plugins/dad-jokes-generator/
 * Description: Display a random dad joke on your page with a shortcode [dad-jokes]
 * Version: 1.0
 * Author: Adam Langley, AO Digital
 * Author URI: https://aodigital.com.au
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dad-jokes-generator
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function dadjokeg_shortcode() {
    wp_enqueue_style('dadjokeg_style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('dadjokeg_script', plugins_url('script.js', __FILE__), array(), false, true);
    wp_localize_script('dadjokeg_script', 'dadJokesAjax', array('ajax_url' => admin_url('admin-ajax.php')));

    $content = '<div id="dadjokeg_joke-container">
                    <p id="dadjokeg_setup"></p>
                    <p id="dadjokeg_punchline"></p>
                </div>
                <button id="dadjokeg_new-joke-btn">Get New Joke</button>';
    return $content;
}
add_shortcode('dad-jokes', 'dadjokeg_shortcode');


function dadjokeg_fetch_joke() {
    $jokes_api_url = 'https://icanhazdadjoke.com/';
    $response = wp_remote_get($jokes_api_url, array('headers' => array('Accept' => 'application/json')));
    if (is_wp_error($response)) {
        // Fallback jokes if API fails
        require 'jokes-backup.php';
        
        $random_joke = $jokes[array_rand($jokes)];
        $random_joke = array('setup' => $random_joke, 'punchline' => '');
    } else {
        $joke_data = json_decode(wp_remote_retrieve_body($response), true);
        $random_joke = array('setup' => $joke_data['joke'], 'punchline' => '');
    }

    // Escaping the joke text
    $random_joke['setup'] = wp_kses_post($random_joke['setup']);
    $random_joke['punchline'] = wp_kses_post($random_joke['punchline']);

    wp_send_json_success($random_joke);
}

add_action('wp_ajax_nopriv_dadjokeg_fetch_joke', 'dadjokeg_fetch_joke');
add_action('wp_ajax_dadjokeg_fetch_joke', 'dadjokeg_fetch_joke');

?>