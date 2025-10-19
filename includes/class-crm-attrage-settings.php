<?php

class CRM_Attrage_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
    }

    public function add_admin_menu() {
        add_options_page(
            'CRM Attrage Settings',
            'CRM Attrage',
            'manage_options',
            'crm_attrage',
            array( $this, 'options_page' )
        );
    }

    public function settings_init() {
        register_setting( 'crm_attrage_options', 'crm_attrage_settings' );

        add_settings_section(
            'crm_attrage_section_api',
            __( 'Configurações da API do CRM Attrage', 'crm-attrage' ),
            array( $this, 'crm_attrage_section_api_callback' ),
            'crm_attrage'
        );

        add_settings_field(
            'crm_attrage_api_endpoint',
            __( 'Endpoint da API', 'crm-attrage' ),
            array( $this, 'crm_attrage_api_endpoint_callback' ),
            'crm_attrage',
            'crm_attrage_section_api'
        );

        add_settings_field(
            'crm_attrage_api_token',
            __( 'Token de Acesso da API', 'crm-attrage' ),
            array( $this, 'crm_attrage_api_token_callback' ),
            'crm_attrage',
            'crm_attrage_section_api'
        );
    }

    public function crm_attrage_section_api_callback() {
        echo __( 'Configure o endpoint da API do seu CRM Attrage e o token de acesso.', 'crm-attrage' );
    }

    public function crm_attrage_api_endpoint_callback() {
        $options = get_option( 'crm_attrage_settings' );
        ?>
        <input type="text" name="crm_attrage_settings[api_endpoint]" value="<?php echo isset( $options['api_endpoint'] ) ? esc_attr( $options['api_endpoint'] ) : ''; ?>" class="regular-text" placeholder="Ex: https://seucrm.com.br/api/leads">
        <?php
    }

    public function crm_attrage_api_token_callback() {
        $options = get_option( 'crm_attrage_settings' );
        ?>
        <input type="password" name="crm_attrage_settings[api_token]" value="<?php echo isset( $options['api_token'] ) ? esc_attr( $options['api_token'] ) : ''; ?>" class="regular-text">
        <?php
    }

    public function options_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'crm_attrage_options' );
                do_settings_sections( 'crm_attrage' );
                submit_button( 'Salvar Configurações' );
                ?>
            </form>
        </div>
        <?php
    }
}

