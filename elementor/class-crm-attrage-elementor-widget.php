<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class CRM_Attrage_Elementor_Widget extends Widget_Base {

    public function get_name() {
        return 'crm_attrage_form_elementor';
    }

    public function get_title() {
        return __( 'Formulário CRM Attrage', 'crm-attrage' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_form_fields',
            [
                'label' => __( 'Campos do Formulário', 'crm-attrage' ),
            ]
        );

        $this->add_control(
            'funnel_stage_id_hash',
            [
                'label'       => __( 'ID do Funil (Hash)', 'crm-attrage' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __( 'Insira o hash do ID do funil', 'crm-attrage' ),
                'description' => __( 'Este campo será enviado de forma oculta para a API do CRM.', 'crm-attrage' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'field_type',
            [
                'label'   => __( 'Tipo de Campo', 'crm-attrage' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'text'     => __( 'Texto', 'crm-attrage' ),
                    'email'    => __( 'Email', 'crm-attrage' ),
                    'number'   => __( 'Número', 'crm-attrage' ),
                    'tel'      => __( 'Telefone', 'crm-attrage' ),
                    'checkbox' => __( 'Checkbox', 'crm-attrage' ),
                    'hidden'   => __( 'Oculto', 'crm-attrage' ),
                ],
            ]
        );

        $repeater->add_control(
            'field_label',
            [
                'label'     => __( 'Rótulo do Campo', 'crm-attrage' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => __( 'Campo', 'crm-attrage' ),
                'condition' => [
                    'field_type!' => ['hidden']
                ]
            ]
        );

        $repeater->add_control(
            'field_name',
            [
                'label'       => __( 'Nome do Campo (API)', 'crm-attrage' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __( 'Ex: name, email, phone', 'crm-attrage' ),
                'description' => __( 'Nome do campo como esperado pela API do CRM. Ex: name, email, phone, contract_zap, produto, amount.', 'crm-attrage' ),
            ]
        );

        $repeater->add_control(
            'field_required',
            [
                'label'     => __( 'Obrigatório', 'crm-attrage' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __( 'Sim', 'crm-attrage' ),
                'label_off' => __( 'Não', 'crm-attrage' ),
                'default'   => '',
                'condition' => [
                    'field_type!' => ['hidden']
                ]
            ]
        );

        $repeater->add_control(
            'field_value',
            [
                'label'       => __( 'Valor Padrão (para campos ocultos)', 'crm-attrage' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __( 'Valor a ser enviado', 'crm-attrage' ),
                'condition'   => [
                    'field_type' => 'hidden'
                ]
            ]
        );

        $this->add_control(
            'form_fields',
            [
                'label' => __( 'Adicionar Campos', 'crm-attrage' ),
                'type'  => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'field_type'  => 'text',
                        'field_label' => __( 'Nome', 'crm-attrage' ),
                        'field_name'  => 'name',
                        'field_required' => 'yes',
                    ],
                    [
                        'field_type'  => 'email',
                        'field_label' => __( 'Email', 'crm-attrage' ),
                        'field_name'  => 'email',
                        'field_required' => 'yes',
                    ],
                    [
                        'field_type'  => 'text',
                        'field_label' => __( 'Telefone', 'crm-attrage' ),
                        'field_name'  => 'phone',
                    ],
                ],
                'title_field' => '{{{ field_label }}} ({{{ field_name }}})',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_submit_button',
            [
                'label' => __( 'Botão de Envio', 'crm-attrage' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'   => __( 'Texto do Botão', 'crm-attrage' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Enviar Lead', 'crm-attrage' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $funnel_stage_id_hash = $settings['funnel_stage_id_hash'];

        if ( empty( $funnel_stage_id_hash ) ) {
            echo '<div class="elementor-alert elementor-alert-danger">' . __( 'Erro: O ID do Funil (Hash) não foi configurado para este formulário.', 'crm-attrage' ) . '</div>';
            return;
        }

        ob_start();
        ?>
        <div class="crm-attrage-form-container crm-attrage-elementor-form">
            <form id="crm-attrage-lead-form-elementor" method="post">
                <?php foreach ( $settings['form_fields'] as $field ) : ?>
                    <?php
                    $field_id = $this->get_id() . '_' . $field['_id'];
                    $field_name = sanitize_key( $field['field_name'] );
                    $field_label = esc_html( $field['field_label'] );
                    $field_type = esc_attr( $field['field_type'] );
                    $field_required = ( 'yes' === $field['field_required'] ) ? 'required' : '';
                    $field_value = esc_attr( $field['field_value'] );
                    ?>

                    <?php if ( 'hidden' === $field_type ) : ?>
                        <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>">
                    <?php else : ?>
                        <p>
                            <label for="<?php echo $field_id; ?>"><?php echo $field_label; ?>:</label>
                            <?php if ( 'checkbox' === $field_type ) : ?>
                                <input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="1" <?php echo $field_required; ?>>
                            <?php else : ?>
                                <input type="<?php echo $field_type; ?>" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $field_required; ?>>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php endforeach; ?>

                <input type="hidden" name="action" value="crm_attrage_submit_form_elementor">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'crm_attrage_form_nonce_elementor' ); ?>">
                <input type="hidden" name="funnel_stage_id" value="<?php echo esc_attr( $funnel_stage_id_hash ); ?>">
                <input type="submit" value="<?php echo esc_attr( $settings['button_text'] ); ?>">
                <div id="crm-attrage-form-message-<?php echo $this->get_id(); ?>" class="crm-attrage-form-message"></div>
            </form>
        </div>
        <?php
        echo ob_get_clean();
    }

    protected function _content_template() {
        ?>
        <div class="crm-attrage-form-container crm-attrage-elementor-form">
            <# if ( '' === settings.funnel_stage_id_hash ) { #>
                <div class="elementor-alert elementor-alert-danger">Erro: O ID do Funil (Hash) não foi configurado para este formulário.</div>
            <# } else { #>
                <form id="crm-attrage-lead-form-elementor" method="post">
                    <# _.each( settings.form_fields, function( field ) { #>
                        <#
                        var fieldId = view.getID() + '_' + field._id;
                        var fieldName = field.field_name;
                        var fieldLabel = field.field_label;
                        var fieldType = field.field_type;
                        var fieldRequired = ( 'yes' === field.field_required ) ? 'required' : '';
                        var fieldValue = field.field_value;
                        #>

                        <# if ( 'hidden' === fieldType ) { #>
                            <input type="hidden" name="{{{ fieldName }}}" value="{{{ fieldValue }}}">
                        <# } else { #>
                            <p>
                                <label for="{{{ fieldId }}}">{{{ fieldLabel }}}:</label>
                                <# if ( 'checkbox' === fieldType ) { #>
                                    <input type="checkbox" id="{{{ fieldId }}}" name="{{{ fieldName }}}" value="1" {{{ fieldRequired }}}> 
                                <# } else { #>
                                    <input type="{{{ fieldType }}}" id="{{{ fieldId }}}" name="{{{ fieldName }}}" {{{ fieldRequired }}}> 
                                <# } #>
                            </p>
                        <# } #>
                    <# } ); #>

                    <input type="hidden" name="action" value="crm_attrage_submit_form_elementor">
                    <input type="hidden" name="_wpnonce" value="">
                    <input type="hidden" name="funnel_stage_id" value="{{{ settings.funnel_stage_id_hash }}}">
                    <input type="submit" value="{{{ settings.button_text }}}">
                    <div class="crm-attrage-form-message"></div>
                </form>
            <# } #>
        </div>
        <?php
    }
}

