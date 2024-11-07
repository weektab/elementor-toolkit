<?php
/*
Plugin Name: Elementor Widgets
Plugin URI: https://weektab.org/
Description: Our Elementor widgets are available within the editor backend, allowing for seamless integration via drag-and-drop.
Version: 1.0
Author: Weektab
Author URI: https://app.weektab.org/@weektab
*/

// Register the function to generate the QR code for each page
add_action('add_meta_boxes', 'generate_qr_code_meta_box');
function generate_qr_code_meta_box() {
    add_meta_box('qr_code_meta_box', 'QR Code', 'display_qr_code_meta_box', 'page', 'side');
}

// Function to display the QR code meta box in the backend
function display_qr_code_meta_box($post) {
    $permalink = get_permalink($post->ID);
    $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($permalink) . '&size=150x150';

    // Output the QR code image
    echo '<img src="' . $qr_code_url . '" alt="QR Code" />';
}

// Register the Elementor widget
add_action('elementor/widgets/widgets_registered', 'register_qr_code_elementor_widget');
function register_qr_code_elementor_widget() {
    if (class_exists('Elementor\\Widget_Base')) {
        class QR_Code_Elementor_Widget extends \Elementor\Widget_Base {
            public function get_name() {
                return 'qr-code-widget';
            }

            public function get_title() {
                return 'QR Code';
            }

            public function get_icon() {
                return 'eicon-frame-expand';
            }

            public function get_categories() {
                return ['general'];
            }

            protected function _register_controls() {
                $this->start_controls_section(
                    'section_content',
                    [
                        'label' => 'Content',
                    ]
                );

                $this->add_control(
                    'qr_code_size',
                    [
                        'label' => 'QR Code Size',
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 150,
                        'min' => 50,
                        'max' => 500,
                    ]
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $current_page = get_queried_object();
                $permalink = get_permalink($current_page->ID);
                $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($permalink) . '&size=' . $settings['qr_code_size'] . 'x' . $settings['qr_code_size'];

                echo '<div class="qr-code-widget">';
                echo '<img src="' . $qr_code_url . '" alt="QR Code" />';
                echo '</div>';
            }
        }

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new QR_Code_Elementor_Widget());
    }
}