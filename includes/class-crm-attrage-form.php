<?php

class CRM_Attrage_Form {

    public function __construct() {
        add_shortcode( 'crm_attrage_form', array( $this, 'render_form' ) );
        add_shortcode( 'crm_attrage_form_basic', array( $this, 'render_basic_form' ) );
        add_shortcode( 'crm_attrage_form_name_email', array( $this, 'render_name_email_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_nopriv_crm_attrage_submit_form', array( $this, 'submit_form' ) );
        add_action( 'wp_ajax_crm_attrage_submit_form', array( $this, 'submit_form' ) );
        add_action( 'wp_ajax_nopriv_crm_attrage_submit_form_elementor', array( $this, 'submit_form_elementor' ) );
        add_action( 'wp_ajax_crm_attrage_submit_form_elementor', array( $this, 'submit_form_elementor' ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'crm-attrage-form-script', plugin_dir_url( __FILE__ ) . '../assets/js/crm-attrage-form.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'crm-attrage-form-script', 'crmAttrage', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'crm_attrage_form_nonce' ),
        ) );

        // Script para o formulário do Elementor
        wp_enqueue_script( 'crm-attrage-form-elementor-script', plugin_dir_url( __FILE__ ) . '../assets/js/crm-attrage-form-elementor.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'crm-attrage-form-elementor-script', 'crmAttrageElementor', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'crm_attrage_form_nonce_elementor' ),
        ) );
        wp_enqueue_style( 'crm-attrage-form-style', plugin_dir_url( __FILE__ ) . '../assets/css/crm-attrage-form.css', array(), '1.0.0' );
    }

    public function render_form( $atts ) {
        $atts = shortcode_atts( array(
            'funnel_stage_id' => '',
        ), $atts, 'crm_attrage_form' );

        $funnel_stage_id_attr = sanitize_text_field( $atts['funnel_stage_id'] );
        ob_start();
        ?>
        <div class="crm-attrage-form-container">
            <form id="crm-attrage-lead-form" method="post">
                <p>
                    <label for="crm_attrage_name">Nome:</label>
                    <input type="text" id="crm_attrage_name" name="name" required>
                </p>
                <p>
                    <label for="crm_attrage_phone">Telefone:</label>
                    <input type="text" id="crm_attrage_phone" name="phone">
                </p>
                <p>
                    <label for="crm_attrage_cpf">CPF:</label>
                    <input type="text" id="crm_attrage_cpf" name="cpf">
                </p>
                <p>
                    <label for="crm_attrage_email">Email:</label>
                    <input type="email" id="crm_attrage_email" name="email">
                </p>
                <p>
                    <label for="crm_attrage_contract_zap">Contratar via WhatsApp:</label>
                    <input type="checkbox" id="crm_attrage_contract_zap" name="contract_zap" value="1">
                </p>
                <p>
                    <label for="crm_attrage_contract_email">Contratar via Email:</label>
                    <input type="checkbox" id="crm_attrage_contract_email" name="contract_email" value="1">
                </p>
                <p>
                    <label for="crm_attrage_contract_phone">Contratar via Telefone:</label>
                    <input type="checkbox" id="crm_attrage_contract_phone" name="contract_phone" value="1">
                </p>
                <p>
                    <label for="crm_attrage_produto">Produto:</label>
                    <input type="text" id="crm_attrage_produto" name="produto">
                </p>
                <p>
                    <label for="crm_attrage_amount">Valor:</label>
                    <input type="number" id="crm_attrage_amount" name="amount" step="0.01">
                </p>
                <p>
                    <?php if ( empty( $funnel_stage_id_attr ) ) : ?>
                        <label for="crm_attrage_funnel_stage_id">ID do Funil (Obrigatório):</label>
                        <input type="number" id="crm_attrage_funnel_stage_id" name="funnel_stage_id" required>
                    <?php else : ?>
                        <input type="hidden" name="funnel_stage_id" value="<?php echo esc_attr( $funnel_stage_id_attr ); ?>">
                    <?php endif; ?>
                </p>
                <p>
                    <input type="hidden" name="action" value="crm_attrage_submit_form">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'crm_attrage_form_nonce' ); ?>">
                    <input type="submit" value="Enviar Lead">
                </p>
                <div id="crm-attrage-form-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_name_email_form( $atts ) {
        $atts = shortcode_atts( array(
            'funnel_stage_id' => '',
        ), $atts, 'crm_attrage_form_name_email' );

        $funnel_stage_id_attr = sanitize_text_field( $atts['funnel_stage_id'] );

        if ( empty( $funnel_stage_id_attr ) ) {
            return '<p style="color: red;">Erro: O shortcode `[crm_attrage_form_name_email]` requer o atributo `funnel_stage_id`.</p>';
        }

        ob_start();
        ?>
        <div class="crm-attrage-form-container">
            <form id="crm-attrage-lead-form" method="post">
                <p>
                    <label for="crm_attrage_name_ne">Nome:</label>
                    <input type="text" id="crm_attrage_name_ne" name="name" required>
                </p>
                <p>
                    <label for="crm_attrage_email_ne">Email:</label>
                    <input type="email" id="crm_attrage_email_ne" name="email" required>
                </p>
                <p>
                    <input type="hidden" name="funnel_stage_id" value="<?php echo esc_attr( $funnel_stage_id_attr ); ?>">
                    <input type="hidden" name="action" value="crm_attrage_submit_form">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'crm_attrage_form_nonce' ); ?>">
                    <input type="submit" value="Enviar Lead">
                </p>
                <div id="crm-attrage-form-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_basic_form() {
        ob_start();
        ?>
        <div class="crm-attrage-form-container">
            <form id="crm-attrage-lead-form" method="post">
                <p>
                    <label for="crm_attrage_name_basic">Nome:</label>
                    <input type="text" id="crm_attrage_name_basic" name="name" required>
                </p>
                <p>
                    <label for="crm_attrage_funnel_stage_id_basic">ID do Funil (Obrigatório):</label>
                    <input type="number" id="crm_attrage_funnel_stage_id_basic" name="funnel_stage_id" required>
                </p>
                <p>
                    <input type="hidden" name="action" value="crm_attrage_submit_form">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'crm_attrage_form_nonce' ); ?>">
                    <input type="submit" value="Enviar Lead Básico">
                </p>
                <div id="crm-attrage-form-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function submit_form_elementor() {
        check_ajax_referer( 'crm_attrage_form_nonce_elementor', '_wpnonce' );

        $options = get_option( 'crm_attrage_settings' );
        $api_endpoint = isset( $options['api_endpoint'] ) ? $options['api_endpoint'] : '';
        $api_token = isset( $options['api_token'] ) ? $options['api_token'] : '';

        if ( empty( $api_endpoint ) || empty( $api_token ) ) {
            wp_send_json_error( array( 'message' => 'Erro: Endpoint da API ou Token de Acesso não configurados.' ) );
        }

        $data = array();
        // Coletar dados dos campos dinâmicos do Elementor
        foreach ( $_POST as $key => $value ) {
            if ( in_array( $key, array( 'action', '_wpnonce' ) ) ) {
                continue;
            }
            // Sanitize e adicionar ao array de dados
            if ( is_array( $value ) ) {
                $data[sanitize_key( $key )] = array_map( 'sanitize_text_field', $value );
            } else if ( is_email( $value ) ) {
                $data[sanitize_key( $key )] = sanitize_email( $value );
            } else if ( is_numeric( $value ) ) {
                $data[sanitize_key( $key )] = floatval( $value );
            } else {
                $data[sanitize_key( $key )] = sanitize_text_field( $value );
            }
        }

        // O funnel_stage_id é obrigatório e já vem do Elementor como um hidden field
        if ( empty( $data['funnel_stage_id'] ) ) {
            wp_send_json_error( array( 'message' => 'Erro: ID do Funil (funnel_stage_id) é obrigatório e não foi fornecido.' ) );
        }

        $response = wp_remote_post( $api_endpoint, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_token,
            ),
            'body'        => json_encode( $data ),
            'method'      => 'POST',
            'data_format' => 'body',
            'timeout'     => 30,
        ) );
        // wp_send_json_error( array($data) );
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            wp_send_json_error( array( 'message' => 'Erro ao conectar com o CRM: ' . $error_message ) );
        } else {
            $body = wp_remote_retrieve_body( $response );
            $http_code = wp_remote_retrieve_response_code( $response );
            $response_data = json_decode( $body, true );

            if ( $http_code >= 200 && $http_code < 300 ) {
                wp_send_json_success( array( 'message' => 'Lead enviado com sucesso!', 'response' => $response_data ) );
            } else {
                $error_message = isset( $response_data['message'] ) ? $response_data['message'] : 'Erro desconhecido ao enviar lead.';
                wp_send_json_error( array( 'message' => 'Erro do CRM (' . $http_code . '): ' . $error_message, 'response' => $response_data ) );
            }
        }

        wp_die();
    }

    public function submit_form() {
        check_ajax_referer( 'crm_attrage_form_nonce', '_wpnonce' );

        $options = get_option( 'crm_attrage_settings' );
        $api_endpoint = isset( $options['api_endpoint'] ) ? $options['api_endpoint'] : '';
        $api_token = isset( $options['api_token'] ) ? $options['api_token'] : '';

        if ( empty( $api_endpoint ) || empty( $api_token ) ) {
            wp_send_json_error( array( 'message' => 'Erro: Endpoint da API ou Token de Acesso não configurados.' ) );
        }

        $data = array(
            'name'           => sanitize_text_field( $_POST['name'] ),
            'phone'          => sanitize_text_field( $_POST['phone'] ),
            'cpf'            => sanitize_text_field( $_POST['cpf'] ),
            'email'          => sanitize_email( $_POST['email'] ),
            'contract_zap'   => isset( $_POST['contract_zap'] ) ? 1 : 0,
            'contract_email' => isset( $_POST['contract_email'] ) ? 1 : 0,
            'contract_phone' => isset( $_POST['contract_phone'] ) ? 1 : 0,
            'produto'        => sanitize_text_field( $_POST['produto'] ?? 'Landig-page' ),
            'amount'         => floatval( $_POST['amount'] ),
            'funnel_stage_id' => intval( $_POST['funnel_stage_id'] ),
        );

        // Validar campos obrigatórios
        if ( empty( $data['name'] ) || empty( $data['funnel_stage_id'] ) ) {
            wp_send_json_error( array( 'message' => 'Erro: Nome e ID do Funil são campos obrigatórios.' ) );
        }

        $response = wp_remote_post( $api_endpoint, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_token,
            ),
            'body'        => json_encode( $data ),
            'method'      => 'POST',
            'data_format' => 'body',
            'timeout'     => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            wp_send_json_error( array( 'message' => 'Erro ao conectar com o CRM: ' . $error_message ) );
        } else {
            $body = wp_remote_retrieve_body( $response );
            $http_code = wp_remote_retrieve_response_code( $response );
            $response_data = json_decode( $body, true );

            if ( $http_code >= 200 && $http_code < 300 ) {
                wp_send_json_success( array( 'message' => 'Lead enviado com sucesso! ->2', 'response' => $response_data ) );
            } else {
                $error_message = isset( $response_data['message'] ) ? $response_data['message'] : 'Erro desconhecido ao enviar lead.';
                wp_send_json_error( array( 'message' => 'Erro do CRM (' . $http_code . '): ' . $error_message, 'response' => $response_data ) );
            }
        }

        wp_die();
    }
}

