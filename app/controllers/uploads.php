<?php

/**
 * Allows access to files uploaded to the uploads directory, which likely resides
 * above a publically accessible directory
 *
 * @package blesta
 * @subpackage blesta.app.controllers
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Uploads extends AppController
{
    /**
     * Pre-action setup method that is called before the index method, or the set controller action
     */
    public function preAction()
    {
        parent::preAction();

        $this->components(['Download']);
        $this->uses(['Companies']);
    }

    /**
     * Can not access this resource
     */
    public function index()
    {
        $this->redirect('404');
    }

    /**
     * Handle invoice logos and backgrounds
     */
    public function invoices()
    {
        if (!isset($this->get[0])) {
            $this->redirect('404');
        }

        $type = strtolower($this->get[0]);

        switch ($type) {
            case 'inv_logo':
                break;
            case 'inv_background':
                break;
            default:
                $this->redirect('404');
        }

        $image = $this->Companies->getSetting($this->company_id, $type);

        if ($image && file_exists($image->value)) {
            $this->Download->streamFile($image->value);
            exit;
        }
        $this->redirect('404');
    }
}
