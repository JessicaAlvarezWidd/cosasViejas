<?phpclass Archivo {    public static function Subir()    {		//IMPLEMENTAR...    	$resultado = new stdClass();    	$resultado->exito = TRUE;    	if (!file_exists($_FILES["imagenModificada"]["tmp_name"]))    	{    		$resultado->exito = FALSE;			$resultado->mensaje = "Error al subir la imagen. ";			return $resultado;    	}    	$tipoArchivo = pathinfo($_FILES["imagenModificada"]["name"], PATHINFO_EXTENSION);    	date_default_timezone_set("America/Argentina/Buenos_Aires");		$archivoTmp = $_POST["idFoto"] . "_" . date("Ymd_His") . "." . $tipoArchivo;		$destino = "tmp/" . $archivoTmp;		if(getimagesize($_FILES["imagenModificada"]["tmp_name"]) === false)		{			$resultado->exito = FALSE;			$resultado->mensaje = "El archivo seleccionado no es una imagen. ";			return $resultado;		}		if($tipoArchivo != "jpg" && $tipoArchivo != "jpeg" && $tipoArchivo != "gif" && $tipoArchivo != "png")		{				$resultado->exito = FALSE;				$resultado->mensaje = "Tipo de imagen no permitido. ";				return $resultado;		}		if (file_exists($destino))		{			$$resultado->exito = FALSE;			$resultado->mensaje = "La imagen ya se ha subido. ";			return $resultado;		}		if ($_FILES["imagenModificada"]["size"] > 500000)		{			$resultado->exito = FALSE;			$resultado->mensaje = "La imagen supera el peso maximo. ";			return $resultado;		}		if (!move_uploaded_file($_FILES["imagenModificada"]["tmp_name"], $destino))		{			$resultado->exito = FALSE;			$resultado->mensaje = "La imagen no se pudo guardar. ";			return $resultado;		}		if (substr_count($_POST["fotoAnterior"], "tmp/") > 0)			Archivo::Borrar($_POST["fotoAnterior"]);		// Preguntar que hacer con los archivos temporales.				$resultado->imagenSubida = $archivoTmp;		$resultado->imagenSubidaRuta = $destino;		return $resultado;    }    public static function Borrar($path)    {		//IMPLEMENTAR...		return unlink($path);    }    public static function Mover($pathOrigen, $pathDestino)    {		//IMPLEMENTAR...		if (!file_exists($pathOrigen) || file_exists($pathDestino))			return FALSE;		if (copy($pathOrigen, $pathDestino))			return Archivo::Borrar($pathOrigen);		return FALSE;    }    public static function AgregarEliminadoJSON($obj)	{		require_once "./lib/nusoap.php";		//$host = 'http://localhost/SegundoParcial/Romero.Federico.SPPIII/ws/FuncionesWS.php';		$host = 'http://rfsegundoparcialp3.eshost.com.ar/Romero.Federico.SPPIII/ws/FuncionesWS.php';		$client = new nusoap_client($host . '?wsdl');		$err = $client->getError();		if ($err)			return 'ERROR EN LA CONSTRUCCION DEL WS:\n' . $err . '.';		$result = $client->call('AgregarEliminadoJSON', array($obj));		if ($client->fault)			return 'ERROR AL INVOCAR METODO:\n' . $result . '.';		else 		{			$err = $client->getError();			if ($err)				return 'ERROR EN EL CLIENTE:\n' . $err . '.';			else				return "Se ha podido guardar la informacion de la eliminacion.";		}	}	public static function TraerEliminadosJSONYMostrarGrilla()	{		$resultado = new stdClass();        $resultado->exito = TRUE;		require_once "./lib/nusoap.php";		//$host = 'http://localhost/SegundoParcial/Romero.Federico.SPPIII/ws/FuncionesWS.php';		$host = 'http://rfsegundoparcialp3.eshost.com.ar/Romero.Federico.SPPIII/ws/FuncionesWS.php';		$client = new nusoap_client($host . '?wsdl');		$err = $client->getError();		if ($err)		{			$resultado->exito = FALSE;			$resultado->mensaje = 'ERROR EN LA CONSTRUCCION DEL WS:\n' . $err . '.';		}		else		{			$result = $client->call('TraerEliminadosJSONYMostrarGrilla');			if ($client->fault)			{				$resultado->exito = FALSE;				$resultado->mensaje = 'ERROR AL INVOCAR METODO:\n' . $result . '.';			}			else 			{				$err = $client->getError();				if ($err)				{					$resultado->exito = FALSE;					$resultado->mensaje = 'ERROR EN EL CLIENTE:\n' . $err . '.';				}				else				{					if ($result == "ERROR")					{						$resultado->exito = FALSE;						$resultado->mensaje = 'ERROR AL QUERER ABRIR EL ARCHIVO.';					}					else						$resultado->tabla = $result;				}			}		}		return $resultado;	}}