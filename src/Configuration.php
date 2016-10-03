<?php

namespace Appitized\Configuration;

use Appitized\Configuration\Exceptions\ConfigurationSettingNotFoundException;
use Appitized\Configuration\Exceptions\ConfigurationTypeException;
use Appitized\Configuration\Exceptions\ConfigurationValueMissingException;
use DB;
use Exception;

class Configuration
{

    public function get($key, $default = null)
    {
        $value = $this->fetchValue($key);

        return $value;
    }

    public function set($key, $value)
    {
        if (empty($value)) {
            throw new ConfigurationValueMissingException('No value given for specified setting ' . $key);
        }
        return $this->storeValue($key, $value);
    }

    protected function storeValue($key, $value)
    {
        $setting = $this->fetchObject($key);
        $type = gettype($value);
        if ($type != $setting->type) {
            throw new ConfigurationTypeException('Variable ' . $type . ' does not match. Type must be ' . $setting->type);
        }
        $success = DB::table('settings')
          ->where('key', $key)
          ->update(['value' => $value]);

        return $success;
    }

    protected function fetchValue($key, $default = null)
    {
        $item = DB::table('settings')->where('key', $key)->first();
        if (!$item) {
            throw new ConfigurationSettingNotFoundException('Configuration setting does not exist for key ' . $key);
        }
        $value = $this->getMutator($item);
        settype($value, $item->type);

        return $value;
    }

    public function getAll()
    {
        $results = DB::table('settings')->get();
        $settings = [];

        foreach ($results as $result) {
            $value = $this->getMutator($result);
            settype($value, $result->type);
            $settings[$result->key] = [
              'value' => $value,
              'type' => $result->type
            ];
        }
        return $settings;
    }

    private function getMutator($setting)
    {
        try {
            $mutator = app(config('configuration.settings.mutator_namespace') . '\\' . studly_case($setting->key) . config('configuration.settings.mutator_suffix',
                'Mutator'));
            $value = $mutator->mutate($setting->value);
        } catch (Exception $e) {
            $value = $setting->value;
        }

        return $value;
    }

}
