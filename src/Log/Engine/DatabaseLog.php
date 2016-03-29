<?php
namespace App\Log\Engine;

use Cake\Log\Engine\BaseLog;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class DatabaseLog extends BaseLog
{
    public function __construct($options = [])
    {
        parent::__construct($options);
    }

    public function log($level, $message, array $context = [])
    {
        // $type would be something like kpingpost
        $type = ltrim($context['scope'][0], 'k');
        $table = Inflector::tableize($type);
        $model = Inflector::classify($table);
        // get the model to write to
        $this->Model = TableRegistry::get(Inflector::pluralize($model));

        // convert array values to json
        array_walk($message, function (&$value, $index) {
            if(is_array($value)) {
                $value = json_encode($value, JSON_HEX_QUOT);
            }
        });

        // log method sits inside the model
        $this->Model->log($message);
    }
}