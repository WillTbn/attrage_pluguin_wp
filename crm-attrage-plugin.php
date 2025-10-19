<?php
/**
 * Plugin Name: CRM Attrage Integration
 * Plugin URI: https://attrage.bucardcode.com.br
 * Description: Um plugin para integrar formulários do WordPress com o CRM Attrage.
 * Version: 1.0.0
 * Author: Attrage
 * Author URI: https://attrage.bucardcode.com.br
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Incluir arquivos de classes e funções

require_once plugin_dir_path( __FILE__ ) . 'includes/class-crm-attrage-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-crm-attrage-form.php';

/**
 * Função para inicializar o plugin.
 */
function crm_attrage_init() {
    new CRM_Attrage_Settings();
    new CRM_Attrage_Form();

    // Inicializar a integração com o Elementor
    if ( class_exists( 'Elementor\Plugin' ) && did_action( 'elementor/loaded' ) ) {
        
        add_action( 'elementor/widgets/register', function( $widgets_manager ) {
            require_once( __DIR__ . '/elementor/class-crm-attrage-elementor-widget.php' );
            $widgets_manager->register( new \Elementor\CRM_Attrage_Elementor_Widget() );
        } );
    }


}
add_action( 'plugins_loaded', 'crm_attrage_init' );

// Ativação do plugin
function crm_attrage_activate() {
    // Ações a serem executadas na ativação (ex: criar tabelas, definir opções padrão)
}
register_activation_hook( __FILE__, 'crm_attrage_activate' );

// Desativação do plugin
function crm_attrage_deactivate() {
    // Ações a serem executadas na desativação (ex: limpar opções, remover tabelas)
}
register_deactivation_hook( __FILE__, 'crm_attrage_deactivate' );

