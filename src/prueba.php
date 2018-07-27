<?php

  require __DIR__ . '/../vendor/autoload.php';


  use Acme\Prueba;

  /*
  // Load a single file
  use Noodlehaus\Config;
  $conf = new Config(__DIR__ . '/../config/configuracion.ini');
  $data = $conf->all();

  var_dump($conf->get('desarrollo'));
  s
  */

  use Northwoods\Config\ConfigFactory;
  $config = ConfigFactory::make([
      'directory' => __DIR__ . '/../config/configuracion.ini',
      //'environment' => 'dev',
      'type'  => 'ini'
  ]);


  var_dump( $config->get('database') );

  /*
  use JBZoo\Data\Data; // And others
  use JBZoo\Data\Ini;
  $configIni  = new Data(__DIR__ . '/../config/configuracion.ini', 'desarrollo');
  //var_dump($configIni);

  var_dump($configIni);
  */
  //$p = new Acme\Prueba();

  //$p->listar();
