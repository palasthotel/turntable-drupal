<?php
$form['turntable_client_master_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Master URL'),
    '#default_value' => variable_get('turntable_client_master_url', array(
        'http://turntable-master.palasthotel.de/'
    )),
    '#description' => t('The URL of the linked Turntable Master instance.')
);
$form['#submit'][] = 'turntable_client_admin_settings_submit';