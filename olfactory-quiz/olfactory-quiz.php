<?php
/*
 Plugin Name: Olfactory Quiz
 Description: A plugin to create and display quizzes with progress navigation.
 Version: 1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Custom Post Type 
function olfactory_register_quizzes_cpt() {
    $args = array(
        'label'                 => __('Quiz', 'textdomain'),
        'description'           => __('Post type for quizzes', 'textdomain'),
        'supports'              => array('title', 'editor', 'custom-fields'),
        'public'                => true,
        'show_in_menu'          => true,
        'show_in_rest'          => true,
        'menu_icon'             => 'dashicons-feedback',
        'has_archive'           => true,
        'rewrite'               => array('slug' => 'quizzes'),
    );

    register_post_type('quizzes', $args);
}
add_action('init', 'olfactory_register_quizzes_cpt');

// Meta Box 
function olfactory_add_quiz_meta_box() {
    add_meta_box(
        'quiz_details_meta_box',
        __('Quiz Details', 'textdomain'),
        'olfactory_quiz_meta_box_callback',
        'quizzes',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'olfactory_add_quiz_meta_box');

function olfactory_quiz_meta_box_callback($post) {
    $questions = get_post_meta($post->ID, '_olfactory_questions', true);
    $scoring = get_post_meta($post->ID, '_olfactory_scoring', true);

    wp_nonce_field('olfactory_quiz_meta_box', 'olfactory_quiz_nonce');

    echo '<p><label for="olfactory_questions">' . __('Questions (JSON Format):', 'textdomain') . '</label></p>';
    echo '<textarea id="olfactory_questions" name="olfactory_questions" rows="6" style="width:100%;">' . esc_textarea($questions) . '</textarea>';

    echo '<p><label for="olfactory_scoring">' . __('Scoring Criteria (JSON Format):', 'textdomain') . '</label></p>';
    echo '<textarea id="olfactory_scoring" name="olfactory_scoring" rows="4" style="width:100%;">' . esc_textarea($scoring) . '</textarea>';
}

function olfactory_save_quiz_meta_box_data($post_id) {
    if (!isset($_POST['olfactory_quiz_nonce']) || !wp_verify_nonce($_POST['olfactory_quiz_nonce'], 'olfactory_quiz_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['olfactory_questions'])) {
        update_post_meta($post_id, '_olfactory_questions', sanitize_textarea_field($_POST['olfactory_questions']));
    }

    if (isset($_POST['olfactory_scoring'])) {
        update_post_meta($post_id, '_olfactory_scoring', sanitize_textarea_field($_POST['olfactory_scoring']));
    }
}
add_action('save_post', 'olfactory_save_quiz_meta_box_data');

// Enqueue 
function olfactory_enqueue_assets() {
    wp_enqueue_style(
        'olfactory-quiz-css',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        '1.0'
    );
    wp_enqueue_script(
        'olfactory-quiz-js',
        plugin_dir_url(__FILE__) . 'js/script.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'olfactory_enqueue_assets');

// Shortcode
function olfactory_quiz_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);

    $quiz_id = intval($atts['id']);
    if (!$quiz_id) {
        return '<p>No quiz found.</p>';
    }

    $questions = get_post_meta($quiz_id, '_olfactory_questions', true);
    $scoring = get_post_meta($quiz_id, '_olfactory_scoring', true);

    if (!$questions || !$scoring) {
        return '<p>Quiz data is missing or incomplete.</p>';
    }

    $questions = json_decode($questions, true);
    $scoring = json_decode($scoring, true);

    if (!$questions || !$scoring) {
        return '<p>Invalid quiz data format.</p>';
    }

    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/quiz-template.php';
    return ob_get_clean();
}
add_shortcode('olfactory_quiz', 'olfactory_quiz_shortcode');
