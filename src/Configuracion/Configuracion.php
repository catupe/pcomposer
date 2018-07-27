<?php
	namespace Configuracion;
	//use \lib\Exceptions\ConfiguracionException;
	use \Exception;
	use \stdClass;

	define('PHP_TAB', "\t");

    class Configuracion {

        private $ruta_archivo 		= null;
        private $ambiente       	= null;
        private $datos          	= null;
		private $datos_todos    	= null;
        private $mensaje        	= null;
        private $ruta_ambiente 	 	= null;
        private $datos_separados	= null;

        public function __construct($archivo = "", $ambiente = "", $ruta_ambiente = "", $cliente = "", $trabajo = ""){

			if($archivo == "" OR !file_exists($archivo)){
                //throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
                throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
            }

            $this->ruta_archivo  = $archivo;
            $this->ambiente      = $ambiente;
            $this->ruta_ambiente = $ruta_ambiente;

			if( strcmp($this->ruta_ambiente, "") != 0 ) {
				if( file_exists($this->ruta_ambiente) ) {
					$var_ambiente = @parse_ini_file ($this->ruta_ambiente, true);
					if($var_ambiente){
						$this->ambiente = $var_ambiente["ambiente"];
					}
				}
				else{
					//throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
          throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
				}
			}

            $salida = array();
            $salida = parse_ini_file ($this->ruta_archivo, true);
            if(!$salida){
                //throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
                throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
            }

			// sustituyo las expresiones de cliente y trabajo por el correspondiente
			$salida[$this->ambiente] = preg_replace('/\{CLIENTE\}/', $cliente, $salida[$this->ambiente]);
			$salida[$this->ambiente] = preg_replace('/\{TRABAJO\}/', $trabajo, $salida[$this->ambiente]);

			$this->datos_todos 		= $salida;
            $this->datos 			= $salida[$this->ambiente];
            $this->datos_separados	= $this->separar();
			$this->parseAll(); // los datos se pueden navegar como objetos
            return $this->datos;
        }
        function getRutaArchivo(){
            return $this->ruta_archivo;
        }
        function getDato($dato = ""){
			if(!isset($this->datos[$dato])){
				//throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
        throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
			}
			return $this->datos[$dato];
		}
		function getLoteDirectorio($dato = "", $directorio = ""){
			if(!isset($this->datos[$dato])){
				//throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
        throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
			}

			preg_match("/($directorio)\|(\d+)/", $this->datos[$dato], $matches);
			return $matches[2];
		}
		function getListaDirectorios($dato = ""){
			if(!isset($this->datos[$dato])){
				//throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
        throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
			}
			$directorio_lote = array();
			$dirs = explode(',', $this->datos[$dato]);
			foreach( $dirs as $k => $v ){
				$data = explode('|', $v);
				$directorio_lote[strtolower ($data[0])]["lote"] 		= $data[1];
				$directorio_lote[strtolower ($data[0])]["descripcion"] 	= $data[2];
			}
			return $directorio_lote;
        }
        /*
         * $arr_datos es un hash con el siguiente formato [dato => valor]
         */
        function setDato($ambiente = "desarrollo", $arr_datos){
	        $fp = fopen($this->ruta_archivo, 'w');
	        if(!$fp){
	            //throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
              throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
	        }
			foreach ($arr_datos as $k => $v){
				$this->datos_todos[$ambiente][$k] = $v;
			}

			$str = "";
			foreach($this->datos_todos as $amb => $dat_n1){
				$str .= "[" . $amb . "]".PHP_EOL;
				foreach($dat_n1 as $k_n1 => $dat_n2){
					$str .= $k_n1 . " = " . $dat_n2 . PHP_EOL;
				}
				$str .= PHP_EOL;
			}
			fwrite($fp, $str);
			fclose($fp);
      	}
        public function getAmbiente($ruta_ambiente = ""){
			return $this->ambiente;
        }
        private function set_element(&$path, $data) {
        	return ($key = array_pop($path)) ? $this->set_element($path, array($key=>$data)) : $data;
        }
        private function separar(){
			$salida = array();
			foreach($this->datos as $k_n2 => $dat_n2){
				$porciones = explode(".", $k_n2);
				$arr = $this->set_element($porciones, $dat_n2);
				$arr = array_merge_recursive($salida, $arr);
				$salida = $arr;
			}
			return $salida;
        }
        public function getDatosSeparados(){
        	return $this->datos_separados;
        }
        public function getDatos2Oject(){
        	return json_decode (json_encode ($this->datos_separados), FALSE);
        }
        public function getBasesPermitidas(){
        	if(!isset($this->datos_separados["bases"]["permitidas"]) or
        	   ($this->datos_separados["bases"]["permitidas"] == "")){
        	   	//throw new ConfiguracionException("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
              throw new Exception("ERROR :: ". __CLASS__ . " :: " . __METHOD__ ." line ". __LINE__);
        	}
        	$bases = $this->datos_separados["bases"]["permitidas"];
        	$arr_bases = explode(" ", $bases);
        	return $arr_bases;
        }
        public function basePermitida($basedatos = ""){
        	return in_array($basedatos, $this->getBasesPermitidas());
        }
		private function parseAll(){

			foreach( $this->datos as $k => $v) {
				$campos = explode('.', $k);

				$primero = array_shift($campos);

				if(empty($this->{$primero})) {
					$this->{$primero} = new stdClass();
				}

				if( count($campos) > 0 ){

					$tmp = $this->{$primero};
					$ultimo = null;

					foreach ($campos as $key => $value) {

						if(empty($tmp->{$value})) {
							$tmp->{$value} = new stdClass();
						}
						$ultimo = $tmp;
					  $tmp = $tmp->{$value};

					}
					$ultimo->{$value} = $v;
				}
				else{
					$this->{$primero} = $v;
				}

			}

		}

    }
