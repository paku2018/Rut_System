<?php
namespace App\CustomClass;

use App\CustomClass\CreateImageReceipt;


class TestReceipt {

	public function __construct($venta_id = null, $es_preventa = false){
		if(!empty($venta_id)){

	        $productos = [];
	        $total = 0;
	        $descuento = 0;
	        $pago = 0;
	        $vuelto = 0;
	        $url_pdf='';

	        $venta = [
	            'fecha_emision'=> date('d/m/Y H:i'),
	            'vendedor'=> 'jose hernandez',
	            'cliente'=> null,
	            'empresa'=> null,
	            'documento'=> null,
	            'tipo_pago'=> null,
	            'folio'=> 1111,
	            'total'=> 12345,
	            'descuento'=> 0,
	            'pago'=> 0,
	            'vuelto'=> 0,
	            'observacion'=> '',
	            'despacho'=> '',
	            'entregado'=> '',
	            'factor_venta_moneda'=> '',
	            'moneda_id'=> 0,
	            'sub_total'=> 100,
	            'monto_iva'=> 10,
	            'iva'=> ((int) 19).'%',
	            'es_con_iva'=> true,
	            'es_boleta'=> true,
	            'neto'=> 456,
	            'exento'=> 0,
	        ];

	        $venta_items = [];
	            $venta_items[] = [
	                'producto_id' => 1,
	                'barcode'     => 123456,
	                'descripcion' => 'producto test',
	                'cantidad'    => 1,
	                'precio_venta'=> 1000,
	                'sub_total'   => 1000,
	                'monto_iva'   => 190,
	                'iva'         => 19,
	                'es_con_iva'  => true,
	                'nombre' => 'producto',
	                'precio'=> 1000,
	                'es_exento'=> false,
	            ];

	            $response_taco = [];
	            $url_pdf = '';

	        $numero_documento = 12345;

	        $fecha_documento = date('d/m/Y H:i');
	        $tipo_documento = 'boleta';
	        $items = $venta_items;



	        $comercio = [
	            'nombre'=> 'restaurante don jose',
	            'nombre_simple'=> 'Rest. Don José',
	            'decorador'=> 'Lo mejor',
	            'rut'=>'25.566.062.4',
	            'direccion'=> 'Valdivia',
	            'telefono'=> '912345678',
	        ];

	        $cliente = [
	            'nombre'=> 'cliente',
	            'rut'=>'11111111-1',
	            'direccion'=> 'direccion cliente',
	            'email'=> 'email@email.cl',
	            'telefono'=> 123,
	        ];

	        $cantidad = count($items);

	        $resolucion = '2014';


	        	$cc_observacion = ceil(strlen('esta es la observacion')/45) * 140;
	        	$base_altura = empty($resolucion) ? 2300 : 3100;
	        	$alto = $base_altura +  (($cantidad-3) * 180) + $cc_observacion;
	        	$alto +=145;





	        //Crear imagen
	        $image = new CreateImageReceipt(1200, $alto);


        	//set QR code custom
        	//$image->setQrCodeCustom($url_pdf, $VentaData->id);

        	//SET tipo de documento
        	$image->setDocumento($tipo_documento, $numero_documento, $fecha_documento);

        	//SET datos de comercio
        	$image->setComercio($comercio);


	        //SET datos de cliente
	        $image->setCliente($cliente);

	        //SET datos del vendedor
	        $image->setVendedor('vendedor');

	        //print DETALLES
	        $image->setDetalle();

	        //print productos
	        $total = 0;
	        foreach ($items as $key => $prod) {
	            $image->addItem($prod['cantidad'],$prod['nombre'],$prod['precio'],$prod['barcode']);
	            $total += $prod['cantidad']*$prod['precio'];
	        }
	        if(!empty($resolucion)){
	        		//SET iva
	        			$image->setIva('IVA('.((int) 19).'%):',1);

	    	}
	        //SET total
	        $image->setTotal('TOTAL:', $total);




	        	//SET tipo de pago
	        	$image->setTipoPago('efectivo', [
	        		'observacion'=> 'nada por aqui',
	        		'pago'=> 0,
	        		'vuelto'=> 0,
	        	], true);

	        	//SET datos footer
	        	$image->setFooter($resolucion);


	        //Crear imagen, bien sea para mostrar o crear como archivo
	        //$filename_image = 'print_'.date('His').'.png';
	        $filename_image =  'print_pre_test.png' ;

	        	if(empty($resolucion)){
	        		$image->renderSinQR($mostrar = false, $filename_image, $es_preventa);
	        	}else{
	        		$image->renderSinQR($mostrar = false, $filename_image, $es_preventa);
	        		//$image->render($mostrar = false, $filename_image, $es_preventa);
	        	}

		}
	}

}

?>