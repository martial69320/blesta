<?php
/**
 * Order main controller
 *
 * @package blesta
 * @subpackage blesta.plugins.order
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminMain extends OrderController
{
    /**
     * Pre action
     */
    public function preAction()
    {
        parent::preAction();

        $this->requireLogin();

        $this->uses(['Order.OrderOrders']);

        Language::loadLang('admin_main', null, PLUGINDIR . 'order' . DS . 'language' . DS);
    }

    /**
     * Renders the orders widget
     */
    public function index()
    {
        // Only available via AJAX
        if (!$this->isAjax()) {
            $this->redirect($this->base_uri . 'billing/');
        }

        $this->components(['SettingsCollection']);

        $status = (isset($this->get[0]) ? $this->get[0] : 'pending');
        $page = (isset($this->get[1]) ? (int)$this->get[1] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_added');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        if (isset($this->get[0])) {
            $status = $this->get[0];
        }

        // If no page set, fetch counts
        if (!isset($this->get[1])) {
            $status_count = [
                'pending' => $this->OrderOrders->getListCount('pending'),
                'accepted' => $this->OrderOrders->getListCount('accepted'),
                'fraud' => $this->OrderOrders->getListCount('fraud'),
                'canceled' => $this->OrderOrders->getListCount('canceled'),
            ];
            $this->set('status_count', $status_count);
        }

        $statuses = $this->OrderOrders->getStatuses();
        unset($statuses[$status]);

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('status', $status);
        $this->set('statuses', $statuses);

        $total_results = $this->OrderOrders->getListCount($status);
        $orders = $this->OrderOrders->getList($status, $page, [$sort => $order]);

        // Determine the geo IP
        $system_settings = $this->SettingsCollection->fetchSystemSettings();
        foreach ($orders as $ord) {
            $ord->geo_ip = $this->getGeoIp($ord->ip_address, $system_settings);
        }

        $this->set('orders', $orders);

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $total_results,
                'uri' => $this->base_uri . 'widget/order/admin_main/index/' . $status . '/[p]/',
                'params' => ['sort' => $sort, 'order' => $order],
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(
            isset($this->get['sort']) ? true : (isset($this->get[1]) || isset($this->get[0]) ? false : null)
        );
    }

    /**
     * List related information for a given order
     */
    public function orderInfo()
    {
        // Ensure a department ID was given
        if (!$this->isAjax() || !isset($this->get[0]) ||
            !($order = $this->OrderOrders->get($this->get[0]))) {
            header($this->server_protocol . ' 401 Unauthorized');
            exit();
        }

        $this->uses(['Transactions', 'Services', 'Packages']);

        // Set language for periods
        $periods = $this->Packages->getPricingPeriods();
        foreach ($this->Packages->getPricingPeriods(true) as $period => $lang) {
            $periods[$period . '_plural'] = $lang;
        }

        // Set services
        $services = [];
        foreach ($order->services as $temp) {
            if (($service = $this->Services->get($temp->service_id))) {
                $services[] = $service;
            }
        }

        // JSON-decode the order fraud report we have stored for use in the view
        if (!empty($order->fraud_report)) {
            $order->fraud_report = (array)$this->Json->decode($order->fraud_report);
        }

        $vars = [
            'order' => $order,
            'applied'=> $this->Transactions->getApplied(null, $order->invoice_id),
            'services' => $services,
            'periods' => $periods,
            'transaction_types' => $this->Transactions->transactionTypeNames()
        ];

        // Send the template
        echo $this->partial('admin_main_orderinfo', $vars);

        // Render without layout
        return false;
    }

    /**
     * Outputs the badge response for the current number of orders with the given status
     */
    public function statusCount()
    {
        // Only available via AJAX
        if (!$this->isAjax()) {
            $this->redirect($this->base_uri . 'billing/');
        }

        $this->uses(['Order.OrderOrders']);
        $status = isset($this->get[0]) ? $this->get[0] : 'pending';

        echo $this->OrderOrders->getListCount($status);
        return false;
    }

    /**
     * Settings
     */
    public function settings()
    {

        // Only available via AJAX
        if (!$this->isAjax()) {
            $this->redirect($this->base_uri . 'billing/');
        }

        $this->helpers(['DataStructure']);
        $this->ArrayHelper = $this->DataStructure->create('Array');

        $this->uses(['Order.OrderStaffSettings']);

        $settings = $this->ArrayHelper->numericToKey(
            $this->OrderStaffSettings->getSettings($this->Session->read('blesta_staff_id'), $this->company_id),
            'key',
            'value'
        );
        $this->set('vars', $settings);

        return $this->renderAjaxWidgetIfAsync(false);
    }

    /**
     * Update settings
     */
    public function update()
    {
        $this->uses(['Order.OrderStaffSettings']);

        // Get all overview settings
        if (!empty($this->post)) {
            $this->OrderStaffSettings->setSettings(
                $this->Session->read('blesta_staff_id'),
                $this->company_id,
                $this->post
            );

            $this->flashMessage('message', Language::_('AdminMain.!success.settings_updated', true));
        }

        $this->redirect($this->base_uri . 'billing/');
    }

    /**
     * Update status for the given set of orders
     */
    public function updateStatus()
    {
        if (isset($this->post['order_id'])) {
            $this->OrderOrders->setStatus($this->post);
        }

        $this->flashMessage('message', Language::_('AdminMain.!success.status_updated', true));
        $this->redirect($this->base_uri . 'billing/');
    }

    /**
     * Search orders
     */
    public function search()
    {
        // Restore structure view location of the admin portal
        $this->structure->setDefaultView(APPDIR);
        $this->structure->setView(null, $this->orig_structure_view);

        // Load the Text Parser
        $this->helpers(['TextParser']);

        // Get search criteria
        $search = (isset($this->get['search']) ? $this->get['search'] : '');
        if (isset($this->post['search'])) {
            $search = $this->post['search'];
        }

        // Set page title
        $this->structure->set('page_title', Language::_('AdminMain.search.page_title', true, $search));

        $this->components(['SettingsCollection']);

        $page = (isset($this->get['p']) ? (int)$this->get['p'] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_added');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');


        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('search', $search);

        // Search
        $orders = $this->OrderOrders->search(
            $search,
            $page,
            [$sort => $order, 'orders.id' => $order]
        );

        // Determine the geo IP
        $system_settings = $this->SettingsCollection->fetchSystemSettings();
        foreach ($orders as $ord) {
            $ord->geo_ip = $this->getGeoIp($ord->ip_address, $system_settings);
        }

        $this->set('statuses', $this->OrderOrders->getStatuses());
        $this->set('orders', $orders);
        $this->set('search', $search);

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->OrderOrders->getSearchCount($search),
                'uri' => $this->base_uri . 'widget/order/admin_main/search/',
                'params' => ['p' => '[p]', 'search' => $search, 'sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        if ($this->isAjax()) {
            return $this->renderAjaxWidgetIfAsync(
                isset($this->post['search']) ? null : (isset($this->get['search']) || isset($this->get['sort']))
            );
        }
    }
}
