<?php
namespace App\CustomClass;

use QRCode;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

/**
 * Clase para crear un recibo, factura, boleta, otro en formato de imagen
 *
 * */
class CreateImageReceipt {

	var $lineaY = 30;
	var $ancho = 320;
	var $alto = 700;
	var $img;
	var $background;
	var $text_color;
	var $line_color;
	var $web_download = 'www.controlcash.cl';
	var $fuente;
	var $fuente_opensans;
	var $fuente_bold;
	var $qr_code_image;
	var $bar_code_image;
	var $filename_qr;

	/**
	 * funcion para crear documento en imagen
	 * @param number $ancho ancho del recibo
	 * @param number $alto altura del recibo
	 *
	 * */
	public function __construct($ancho = null, $alto = null){
		if(!empty($ancho)){
			$this->ancho = $ancho;
		}
		if(!empty($alto)){
			$this->alto = $alto;
		}

		$this->filename_qr = storage_path('app/public/receipts/pagocash_qr.png');
		$this->qr_code_image = imagecreatefrompng($this->filename_qr);

		$this->img = imagecreate( $this->ancho, $this->alto);
		$this->background = imagecolorallocate( $this->img, 255, 255, 255 );
		$this->text_color = imagecolorallocate( $this->img, 0, 0, 0 );
		$this->line_color = imagecolorallocate( $this->img, 100, 100, 100 );

		$this->fuente = storage_path('app/fonts/roboto.ttf');
		$this->fuente_bold = storage_path('app/fonts/OpenSans-Bold.ttf');
		$this->fuente_opensans = storage_path('app/fonts/OpenSans-Light.ttf');
	}

	/**
	 * funcion para setear datos del tipo de documento e imprimir
	 * @param string $nombre_documento nombre del tipo de documento
	 * @param string $codigo codigo del documento
	 * @param string $fecha fecha del documento
	 *
	 * */
	public function setDocumento($nombre_documento = 'Recibo', $codigo = '', $fecha = ''){
		$nombre_documento = str_pad($nombre_documento, 55, " ", STR_PAD_BOTH);
		$this->lineaY += 80;
		imagettftext($this->img, 50, 0, 0, $this->lineaY, $this->text_color, $this->fuente_bold, ucwords($nombre_documento));

		$this->lineaY += 80;
		$codigo = str_pad('N°: '.$codigo, 65, " ", STR_PAD_BOTH);
		imagettftext($this->img, 45, 0, 0, $this->lineaY, $this->text_color, $this->fuente_bold, $codigo);

		$this->lineaY += 75;
		$fecha = 'Fecha: '.$fecha;
		$fecha = str_pad($fecha, 30, " ", STR_PAD_BOTH);
		imagettftext($this->img, 50, 0, 0, $this->lineaY, $this->text_color, $this->fuente, $fecha);
	}

	/**
	 * funcion para setear datos del comercio e imprimir
	 * @param array $data [nombre, rut, telefono, direccion]
	 *
	 * */
	public function setComercio($data){
		$nombre = !empty($data['nombre_simple']) ? $data['nombre_simple'] : $data['nombre'];
		$this->lineaY += 75;
		imagettftext($this->img, 50, 0, 35, $this->lineaY, $this->text_color, $this->fuente, $nombre);

		if(!empty($data['decorador'])){
			$this->lineaY += 75;
			imagettftext($this->img, 50, 0, 35, $this->lineaY, $this->text_color, $this->fuente, $data['decorador']);
		}


		$this->lineaY += 75;
		imagettftext($this->img, 50, 0, 35, $this->lineaY, $this->text_color, $this->fuente, "RUT: ".$data['rut']);

		// if(isset($data['telefono']) && !empty($data['telefono'])){
		// 	$this->lineaY += 70;
		// 	imagettftext($this->img, 50, 0, 35, $this->lineaY, $this->text_color, $this->fuente, 'Fono: '.$data['telefono']);
		// }

		//$this->lineaY += 70;

		//imagettftext($this->img, 50, 0, 35, $this->lineaY, $this->text_color, $this->fuente, $data['direccion']);
		$direccion = $data['direccion'];
		$longitud_direccion = strlen($direccion);
		$split = 30;
		$len = ceil($longitud_direccion/$split);

		for($i=0; $i<$len;$i++){
			$this->lineaY += 60;
			$start = $i*$split;
			$text = substr($direccion, $start, $split);
			imagettftext($this->img, 45, 0, 30, $this->lineaY, $this->text_color, $this->fuente, trim($text));
		}

	}

	/**
	 * funcion para setear datos del cliente e imprimir
	 * @param array $data [nombre, rut, email, direccion]
	 *
	 * */
	public function setCliente($data){
		$this->lineaY += 100;
		imagettftext($this->img, 46, 0, 35, $this->lineaY, $this->text_color, $this->fuente, 'Cliente: '.$data['nombre']);

		//$this->lineaY += 70;
		//imagettftext($this->img, 46, 0, 35, $this->lineaY, $this->text_color, $this->fuente, 'RUT: '.$data['rut']);

		//$this->lineaY += 70;
		//imagettftext($this->img, 46, 0, 35, $this->lineaY, $this->text_color, $this->fuente, "E-mail: ".$data['email']);

		//$this->lineaY += 70;
		//imagettftext($this->img, 45, 0, 35, $this->lineaY, $this->text_color, $this->fuente, "Direccion: ".$data['direccion']);
	}

	/**
	 * funcion para setear datos del vendedor e imprimir
	 * @param string $nombre nombre del vendedor
	 *
	 * */
	public function setVendedor($nombre_vendedor){
		$this->lineaY += 70;
		imagettftext($this->img, 40, 0, 35, $this->lineaY, $this->text_color, $this->fuente, "Vendedor: ".$nombre_vendedor);
	}

	/**
	 * funcion para setear datos del vendedor e imprimir
	 * @param string $nombre nombre del vendedor
	 *
	 * */
	public function setMesa($nombre_mesa){
		$this->lineaY += 70;
		imagettftext($this->img, 40, 0, 35, $this->lineaY, $this->text_color, $this->fuente, "Mesa: ".$nombre_mesa);
	}

	/**
	 * funcion para setear el valor de iva
	 * @param number $cantidad cantidad de productos
	 * @param string $producto_nombre
	 * @param number $producto_precio
	 *
	 * */
	public function addItem($cantidad, $producto_nombre, $producto_precio, $barcode=''){
		$this->lineaY += 65;
		imagettftext($this->img, 42, 0, 30, $this->lineaY+10, $this->text_color, $this->fuente, substr($producto_nombre,0, 33));
		$this->lineaY += 70;
		$precio_individual = "$" . number_format(abs($producto_precio), 0, "", ".");
		$print_fila = $producto_precio < 0 ? 'Descuento': $cantidad.' x '.$precio_individual;
		imagettftext($this->img, 42, 0, 30, $this->lineaY+10, $this->text_color, $this->fuente,  $print_fila);

		$producto_precio = (($producto_precio < 0) ? "-" : "") . "$" . number_format(abs($producto_precio*$cantidad), 0, "", ".");
		$producto_precio_text =  str_pad($producto_precio, 10, " ", STR_PAD_LEFT);
		imagettftext($this->img, 42, 0, ($this->ancho/1.34)-85, $this->lineaY+10, $this->text_color, $this->fuente, $producto_precio_text);

	}

	/**
	 * funcion para setear el valor de iva
	 * @param string $item_name
	 * @param number $item_amount
	 * @param bool $showLine
	 *
	 * */
	public function setTotal($item_name, $item_amount, $showLine = false){
		if($showLine){
			$this->lineaY += 20;
			imagesetthickness ( $this->img, 1 );
			imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );
		}

		$this->lineaY += 70;
		$item_name =  str_pad($item_name, 45, " ", STR_PAD_LEFT);
		imagettftext($this->img, 40, 0, 25, $this->lineaY+10, $this->text_color, $this->fuente_bold, $item_name);

		$item_amount = (($item_amount < 0) ? "-" : "") . "" . number_format(abs($item_amount), 0, "", ".");
		$amount_text =  str_pad($item_amount, 10, " ", STR_PAD_LEFT);
		//imagestring( $this->img, 3, ($this->ancho/1.4)-10, $this->lineaY, "$ ".$amount_text, $this->text_color );
		imagettftext($this->img, 40, 0, ($this->ancho/1.4)-10, $this->lineaY+10, $this->text_color, $this->fuente_bold, "$ ".$amount_text);
	}

	/**
	 * funcion para setear el valor de iva
	 * @param string $item_name
	 * @param number $item_amount
	 * @param bool $showLine
	 *
	 * */
	public function setIva($item_name, $item_amount, $showLine = true){
		if($showLine){
			$this->lineaY += 50;
			imagesetthickness ( $this->img, 1 );
			imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );
		}

		$this->lineaY += 70;
		$item_name =  str_pad($item_name, 45, " ", STR_PAD_LEFT);
		imagettftext($this->img, 40, 0, 25, $this->lineaY+10, $this->text_color, $this->fuente_bold, $item_name );

		$item_amount = (($item_amount < 0) ? "-" : "") . " " . number_format(abs($item_amount), 0, "", ".");
		$amount_text =  str_pad($item_amount, 10, " ", STR_PAD_LEFT);
		imagettftext($this->img, 40, 0, ($this->ancho/1.4)-10, $this->lineaY+10, $this->text_color, $this->fuente_bold, "$ ".$amount_text);
		$this->lineaY += 5;
	}

	/**
	 * funcion para setear el valor de iva
	 * @param string $nombre_tipo_pago
	 *
	 * */
	public function setTipoPago($nombre, $extra=['observacion'=>'','pago'=>0,'vuelto'=>0], $showLine = true, $es_preventa = false){
		$observacion=$extra['observacion'];
		if($showLine){
			$this->lineaY += 50;
			imagesetthickness ( $this->img, 3 );
			imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );
		}
		if(!$es_preventa){
			$this->lineaY += 120;
			imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, 'Tipo de pago: '.substr($nombre,0, 55));
			if(strtoupper(trim($nombre))=='EFECTIVO'){
				$pago = number_format(abs($extra['pago']), 0, "", ".");
				$vuelto = number_format(abs($extra['vuelto']), 0, "", ".");
				$this->lineaY += 80;
				imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, 'Pago: '.$pago);
				$this->lineaY += 80;
				imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, 'Vuelto: '.$vuelto);
			}
		}

		if(!empty($observacion)){
			$this->lineaY += 130;
			imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, 'Observación:');

			$longitud_observacion = strlen($extra['observacion']);
			$split = 30;
			$len = ceil($longitud_observacion/$split);

			for($i=0; $i<$len;$i++){
				$this->lineaY += 60;
				$start = $i*$split;
				$text = substr($observacion, $start, $split);
				imagettftext($this->img, 45, 0, 30, $this->lineaY, $this->text_color, $this->fuente, trim($text));
			}
		}
	}

	public function setPropina($propina, $showLine = true){
		if($showLine){
			$this->lineaY += 50;
			imagesetthickness ( $this->img, 3 );
			imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );
		}


			$this->lineaY += 120;
			imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, 'Propina: $ '.number_format(abs($propina), 0, "", "."));
	}

	/**
	 * funcion para setear el footer
	 * @param string $resolucion
	 *
	 * */
	public function setFooter($resolucion = ''){
		$this->lineaY += 100;
		imagesetthickness ( $this->img, 1 );
		imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );

		$this->lineaY += 100;

		$download = str_pad($this->web_download, 25, " ", STR_PAD_BOTH);
		imagettftext($this->img, 50, 0, 50, $this->lineaY, $this->text_color, $this->fuente, $download);

		if(!empty($resolucion)){
			$this->lineaY += 100;
			$resolucion = str_pad('Resolución SII '.$resolucion, 25, " ", STR_PAD_BOTH);
			imagettftext($this->img, 50, 0, 140, $this->lineaY, $this->text_color, $this->fuente_opensans, $resolucion);
		}


		$this->lineaY += 100;
		$gracias_compra = '123456789-0123456789-123456789-123456789-1234567';
		$gracias_compra = str_pad('Gracias por su compra', 25, " ", STR_PAD_BOTH);
		imagettftext($this->img, 50, 0, 50, $this->lineaY, $this->text_color, $this->fuente, $gracias_compra);
	}

	public function setDetalle(){
		$this->lineaY += 105;
		$detalle = str_pad("D E T A L L E", 65, " ", STR_PAD_BOTH);
		imagettftext($this->img, 40, 0, 35, $this->lineaY, $this->text_color, $this->fuente_bold, $detalle);

		$this->lineaY += 40;
		$linea = str_pad("---------------------", 30, " ", STR_PAD_BOTH);
		imagettftext($this->img, 40, 0, 38, $this->lineaY, $this->text_color, $this->fuente, $linea);
	}

	public function setQrCodeCustom($url = '', $venta_id){
        if(!empty($url)){
        	$this->filename_qr = storage_path('app/public/receipts/qr'.$venta_id.'.png');
        	QRCode::url($url)
		        ->setSize(18)
		        ->setMargin(2)
		        ->setOutfile($this->filename_qr)
		        ->png(true);

		    $this->qr_code_image = imagecreatefrompng($this->filename_qr);
        }
	}

	public function setBarcodeCustom($venta_id){

        if(!empty($venta_id)){

        	$this->filename_qr = DNS1D::getBarcodePNGPath(''.$venta_id, 'C128',10,400);
        	//$this->filename_qr = DNS1D::getBarcodePNGPath('4445645656', 'C128');
        	// QRCode::url($url)
		       //  ->setSize(18)
		       //  ->setMargin(2)
		       //  ->setOutfile($this->filename_qr)
		       //  ->png(true);

		    $this->bar_code_image = imagecreatefrompng($this->filename_qr);
		    $this->qr_code_image = null;
        }
	}

	public function setFooterPreventa(){
		$this->lineaY += 100;
		imagesetthickness ( $this->img, 1 );
		imageline( $this->img, 0, $this->lineaY, $this->ancho, $this->lineaY, $this->line_color );

		$this->lineaY += 100;

		$download = str_pad('REALICE EL PAGO EN CAJA', 25, " ", STR_PAD_BOTH);
		imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, $download);



		$this->lineaY += 100;
		$gracias_compra = '123456789-0123456789-123456789-123456789-1234567';
		$gracias_compra = str_pad('Gracias por su compra', 25, " ", STR_PAD_BOTH);
		imagettftext($this->img, 50, 0, 30, $this->lineaY, $this->text_color, $this->fuente, $gracias_compra);
	}



	public function render($show = true, $filename = '',$es_preventa=false){
		header( "Content-type: image/png" );

		$marge_right = 330;
		$marge_bottom = $es_preventa ? 100 : 20;

		$this->qr_code_image = $es_preventa ? $this->bar_code_image : $this->qr_code_image;

		$sx = imagesx($this->qr_code_image);
		$sy = imagesy($this->qr_code_image);

		imagecopy($this->img, $this->qr_code_image, imagesx($this->img) - $sx - $marge_right, imagesy($this->img) - $sy - $marge_bottom, 0, 0, imagesx($this->qr_code_image), imagesy($this->qr_code_image));

		if($show){
			imagepng( $this->img );
		}else{
			$filename = storage_path('app/public/receipts/'.$filename);
			@imagepng( $this->img, $filename );
		}


		@imagecolordeallocate( $this->line_color );
		@imagecolordeallocate( $this->text_color );
		@imagecolordeallocate( $this->background );
		@imagedestroy( $this->img );
	}

	public function renderSinQR($show = true, $filename = '',$es_preventa=false){
		header( "Content-type: image/png" );

		if($show){
			imagepng( $this->img );
		}else{
			$filename = storage_path('app/public/receipts/'.$filename);
			@imagepng( $this->img, $filename );
		}


		@imagecolordeallocate( $this->line_color );
		@imagecolordeallocate( $this->text_color );
		@imagecolordeallocate( $this->background );
		@imagedestroy( $this->img );
	}

	public function render_preview($show = true, $filename = ''){
		header( "Content-type: image/png" );
		if($show){
			imagepng( $this->img );
		}else{
			$filename = storage_path('app/public/receipts/'.$filename);
			@imagepng( $this->img, $filename );
		}

		@imagecolordeallocate( $this->line_color );
		@imagecolordeallocate( $this->text_color );
		@imagecolordeallocate( $this->background );
		@imagedestroy( $this->img );
	}
}

?>