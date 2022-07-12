<?php
/**
 * Order System login controller
 *
 * @package blesta
 * @subpackage blesta.plugins.order.controllers
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Login extends OrderFormController
{
    /**
     * Handle login requests
     */
    public function index()
    {
        $this->uses(['Users', 'Clients']);

        $redirect_to = $this->base_uri . 'order/';

        if (isset($this->post['redirect_to'])) {
            $redirect_to = $this->post['redirect_to'];
        }

        if (!empty($this->post)) {
            // Ensure the IP address is determined automatically by disallowing it from being set
            unset($this->post['ip_address']);

            // Attempt to log user in
            $user_id = $this->Users->login($this->Session, $this->post);

            $response = ['user_id' => $user_id];

            if (($errors = $this->Users->errors())) {
                $response['error'] = $this->setMessage('error', $errors, true, null, false);
            } else {
                $client = $this->Clients->getByUserId($this->Session->read('blesta_id'));

                if (!$client) {
                    $this->Session->clear();
                    $response['error'] = $this->setMessage(
                        'error',
                        Language::_('Users.!error.username.auth', true),
                        true,
                        null,
                        false
                    );
                } else {
                    $this->Session->write('blesta_company_id', Configure::get('Blesta.company_id'));
                    $this->Session->write('blesta_client_id', $client->id);
                    $response['client_id'] = $client->id;
                    $response['csrf_token'] = $this->Form->getCsrfToken();
                }
            }

            // If ajax, send response data
            if ($this->isAjax()) {
                $this->outputAsJson($response);
            }
        }

        if ($this->isAjax()) {
            return false;
        }

        $this->redirect($redirect_to);
    }
}
