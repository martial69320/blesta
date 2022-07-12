<?php
namespace Blesta\Core\Automation\Tasks\Task;

use Blesta\Core\Automation\Tasks\Common\AbstractTask;
use Blesta\Core\Automation\Type\Common\AutomationTypeInterface;
use Language;
use Loader;
use stdClass;

/**
 * The process_renewing_services automation task
 *
 * @package blesta
 * @subpackage blesta.core.Automation.Tasks.Task
 * @copyright Copyright (c) 2018, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ProcessRenewingServices extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(AutomationTypeInterface $task, array $options = [])
    {
        parent::__construct($task, $options);

        Loader::loadModels($this, ['Services']);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // This task cannot be run right now
        if (!$this->isTimeToRun()) {
            return;
        }

        // Log the task has begun
        $this->log(Language::_('Automation.task.process_renewing_services.attempt', true));

        // Execute the process renewing services cron task
        $this->process($this->task->raw());

        // Log the task has completed
        $this->log(Language::_('Automation.task.process_renewing_services.completed', true));
        $this->logComplete();
    }

    /**
     * Processes the task
     *
     * @param stdClass $data The process renewing services task data
     */
    private function process(stdClass $data)
    {
        // Only attempt service renewals if a previous task exists
        if ($data->date_enabled) {
            // Set the date as either the last run date or the last time that the cron task was enabled
            // This is to prevent service renewals when the cron task was disabled for a period of time
            $run_date = (isset($data->date_last_started) ? $data->date_last_started : $data->date_enabled . 'Z');

            if (strtotime($data->date_enabled) >= strtotime($data->date_last_started)) {
                $run_date = $data->date_enabled . 'Z';
            }

            // Fetch all services since the last run date
            $services = $this->Services->getAllRenewablePaid($run_date);

            // Renew the services
            foreach ($services as $service) {
                $this->Services->renew($service->id);

                // Log success/error
                if (($errors = $this->Services->errors())) {
                    $this->log(
                        Language::_(
                            'Automation.task.process_renewing_services.renew_error',
                            true,
                            $service->id_code,
                            $service->client_id_code
                        )
                    );

                    // Reset errors
                    $this->resetErrors($this->Services);
                } else {
                    $this->log(
                        Language::_(
                            'Automation.task.process_renewing_services.renew_success',
                            true,
                            $service->id_code,
                            $service->client_id_code
                        )
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function isTimeToRun()
    {
        return $this->task->canRun(date('c'));
    }
}
