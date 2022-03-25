<?php
namespace App\CustomClass;

use App\CustomClass\CreateImageReceipt;


class CreateReceipt {
    public $filename = '';
    public function __construct($table, $orders, $user){
        if(!empty($orders)){
            $order_code = [$table->id];
            $comment = '';
            foreach($orders as $order){
                $order_code[] = $order->id;
                $comment = $order->comment;
            }
            $order_code_text = implode('/',$order_code);
            $postfix = str_replace("/","_",$order_code_text);
            $this->filename = 'receipt_'.$user->id.'_'.$postfix.'.png';


            $venta_items = [];
            $total = 0;
            foreach ($orders as $order){
                $total += $order->product->sale_price * $order->order_count;
                $venta_items[] = [
                    'producto_id' => $order->product->id,
                    'barcode'     => $order->product->id,
                    'descripcion' => $order->product->name,
                    'cantidad'    => $order->order_count,
                    'nombre' => $order->product->name,
                    'precio'=> $order->product->sale_price,
                ];

                //adding sub orders
                $children = $order->children;
                foreach ($children as $one) {
                    $venta_items[] = [
                        'producto_id' => $one->product_id,
                        'barcode'     => $one->product_id,
                        'descripcion' => $one->detail->name,
                        'cantidad'    => 1,
                        'nombre' => $one->detail->name,
                        'precio'=> $one->detail->sale_price,
                    ];
                }
            }

            $comercio = [
                'nombre'=> $table->restaurant->name,
                'nombre_simple'=> $table->restaurant->name,
                'decorador'=> $table->restaurant->slogan,
                'rut'=> $table->restaurant->rut,
                'direccion'=> $table->restaurant->address,
                'telefono'=> '',
            ];

            $cliente = [
                'nombre'=> '',
                'rut'=>'',
                'direccion'=> '',
                'email'=> '',
                'telefono'=> '',
            ];

            $cantidad = count($venta_items);
            $resolucion = '';
            $cc_observacion = ceil(strlen($comment)/45) * 140;
            $base_altura = empty($resolucion) ? 2300 : 3100;
            $alto = $base_altura +  (($cantidad-3) * 180) + $cc_observacion;
            $alto += 145;

            //Crear imagen
            $image = new CreateImageReceipt(1200, $alto);

            //set QR code custom
            //$image->setQrCodeCustom($url_pdf, $VentaData->id);

            //SET tipo de documento
            $image->setDocumento('          COMANDA', $order_code_text, date('d/m/Y H:i'));

            //SET datos de comercio
            $image->setComercio($comercio);

            //SET datos de cliente
            $image->setCliente($cliente);

            //SET datos del vendedor
            $image->setVendedor($user->name);

            //SET datos de la mesa
            $image->setMesa($table->name);

            //print DETALLES
            $image->setDetalle();

            //print productos
            $total = 0;
            foreach ($venta_items as $key => $prod) {
                $image->addItem($prod['cantidad'], $prod['nombre'], $prod['precio'] );
                $total += $prod['cantidad']*$prod['precio'];
            }
            //if(!empty($resolucion)){
            //  $image->setIva('IVA('.((int) 19).'%):',1);
            //}
            //SET total
            $image->setTotal('TOTAL:', $total);

            //SET tipo de pago
            /**/
            $image->setComentario('Comentario:', $comment, true);
            /**/

            //SET datos footer
            //$image->setFooter($resolucion);

            if(empty($resolucion)){
                $image->renderSinQR($mostrar = false, $this->filename, false);
            }else{
                $image->renderSinQR($mostrar = false, $this->filename, false);
                //$image->render($mostrar = false, $this->filename, false);
            }
        }
    }

}

?>
