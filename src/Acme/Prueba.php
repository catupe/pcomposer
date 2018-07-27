<?php
  namespace Acme;

  use phpseclib\Net\SFTP;

  //define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);

  class Prueba {


    public function __construct() {}


    public function listar() {
      $sftp = new SFTP('192.168.64.173');
      if (!$sftp->login('firstdat', 'firstdat')) {
          exit('Login Failed');
      }
      echo "<pre>";
      print_r($sftp->nlist('/home/firstdat/IN')); // == $sftp->nlist('.')
      print_r($sftp->rawlist('/home/firstdat/IN')); // == $sftp->rawlist('.')
      /*
      print_r($sftp->nlist('/in/alcon/comprobante/in')); // == $sftp->nlist('.')
      print_r($sftp->rawlist('/in/alcon/comprobante/in')); // == $sftp->rawlist('.')
      */

      //echo $sftp->getSFTPLog();
    }
  }
