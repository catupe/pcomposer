<?php

  require __DIR__ . '/../vendor/autoload.php';

  use Configuracion\Configuracion;

  $cfg = new Configuracion('../config/configuracion.ini', 'desarrollo');
  echo "<pre>";
  //var_dump($cfg->getAll());
  /*
  $cfg->parseAll();
  */
  echo "<br>--------------------++++++++++++++------------------<br>";
  var_dump($cfg->brooklyn->lista_directorios);
  echo "<br>--------------------++++++++++++++------------------<br>";
  var_dump($cfg->homero->name);
  echo "<br>--------------------++++++++++++++------------------<br>";
