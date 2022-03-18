<?php
namespace App\CustomClass;

use App\CustomClass\CreateImageReceipt;


class FinalReceipt {
    public $filename = '';
    public $iva = 19;
    public $resolucion = '80 de 2014';

    public function __construct($payment, $user){
    	$document_date = $payment->created_at->format('d/m/Y H:i');
    	$taco = json_decode($payment->taco_data,false);
    	$payment = json_decode($payment->history_data,false);
    	//dd($taco);
    	$folio = 0;
    	if(!empty($taco->response)){
	    	if(!empty($taco->response->folio)){
	    		$folio = $taco->response->folio;
	    	}
    	}

        if(!empty($payment->items)){
            $comment = '';
            $order_code_text = !empty($folio) ? $folio : $payment->id;
            $postfix = str_replace("/","_",$order_code_text);
            $this->filename = 'receipt_'.$user->id.'_'.$postfix.'.png';

            $venta_items = [];
            $total = 0;
            foreach ($payment->items as $order){
                $total += $order->product->sale_price * $order->order_count;
                $venta_items[] = [
                    'producto_id' => $order->product->id,
                    'barcode'     => $order->product->id,
                    'descripcion' => $order->product->name,
                    'cantidad'    => $order->order_count,
                    'nombre' => $order->product->name,
                    'precio'=> $order->product->sale_price,
                ];
            }

            $comercio = [
                'nombre'=> $payment->restaurant->name,
                'nombre_simple'=> $payment->restaurant->name,
                'decorador'=> $payment->restaurant->slogan,
                'rut'=> $payment->restaurant->rut,
                'direccion'=> $payment->restaurant->address,
                'telefono'=> '',
            ];

            $cliente = [
                'nombre'=> 'Predeterminado',
                'rut'=>'',
                'direccion'=> '',
                'email'=> '',
                'telefono'=> '',
            ];

            $cantidad = count($venta_items);
            $resolucion = $this->resolucion;
            $cc_observacion = ceil(strlen($comment)/45) * 140;
            $base_altura = empty($resolucion) ? 2300 : 3100;
            $alto = $base_altura +  (($cantidad-3) * 180) + $cc_observacion;
            $alto += 145;

            //Crear imagen
            $image = new CreateImageReceipt(1200, $alto);

            if(!empty($folio)){
            	$url_pdf = 'https://sii.pagocash.cl/dte/dte_emitidos/pdf/'.$taco->response->dte.'/'.$taco->response->folio.'/1/'.$taco->response->emisor.'/'.$taco->response->fecha.'/'.$taco->response->total;
            	//set QR code custom
            	$image->setQrCodeCustom($url_pdf, $payment->id);
            }


            //SET tipo de documento
            $image->setDocumento(!empty($folio) ? __('BOLETA ELECTRÓNICA') : '              '.strtoupper(__('receipt')) , $order_code_text, $document_date);

            //SET datos de comercio
            $image->setComercio($comercio);

            //SET datos de cliente
            $image->setCliente($cliente);

            //SET datos del vendedor
            $image->setVendedor($user->name);

            //print DETALLES
            $image->setDetalle();

            //print productos
            $total = 0;
            foreach ($venta_items as $key => $prod) {
                $image->addItem($prod['cantidad'], $prod['nombre'], $prod['precio'] );
                $total += $prod['cantidad']*$prod['precio'];
            }

            if(!empty($folio)){
            	$sub_total2 = $total / (($this->iva/100) + 1);
                $monto_iva = $total - $sub_total2;
                $monto_iva = round($monto_iva, 0);
             	$image->setIva('IVA('.((int) $this->iva).'%):', $monto_iva);
            }
            //SET total
            $image->setTotal('SUB-TOTAL:', $total);

            $image->setPropina($payment->tip);

            $image->setDelivery($payment->shipping, false);

            $image->setTotal('TOTAL:', ($total+$payment->tip+$payment->shipping), true);

            //SET tipo de pago
            /*$image->setTipoPago($this->getPaymentMethod($payment->payment_method), [
                'observacion'=> $comment,
                'pago'=> 0,
                'vuelto'=> 0,
            ], true);*/

            //SET datos footer
            $image->setFooter(!empty($folio) ? $resolucion : '');

            if(empty($folio)){
                $image->renderSinQR($mostrar = false, $this->filename, false);
            }else{
                //$image->renderSinQR($mostrar = false, $this->filename, false);
                $image->render($mostrar = false, $this->filename, false);
            }
        }
    }

    public function getPaymentMethod($type){
    	$data = [
    		1=> __('cash'),
			2=> __('credit_or_debit'),
			3=> __('transfer'),
			4=> __('other'),
    	];
    	return isset($data[$type]) ? $data[$type] : '';
    }

}

?>