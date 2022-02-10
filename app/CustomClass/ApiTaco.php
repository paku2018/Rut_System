<?php
namespace App\CustomClass;

use GuzzleHttp\Client as ClientApi;
use GuzzleHttp\Exception\ConnectException;


/**
 * Clase para emision de boletas y facturas desde la api de Taco pagocash
 *
 * */
class ApiTaco {
    var $taco_data_user = [];
    var $host_api = '';

    private $venta;
    private $venta_items;

    public function __construct($user_id = null){
        $this->taco_data_user = session('taco_data_user', function() { return []; });
        $this->host_api = env('TACO_API_URL_PROD','_EMPTY_');
        // \Log::debug([
        //     'user_id'=> $user_id ,
        //     'this->taco_data_user '=> $this->taco_data_user ,
        //     'this->host_api'=> $this->host_api,
        // ]);
        if(!empty($user_id)){
            if(empty($this->taco_data_user)){
                $this->tokenTacoVendedor($user_id);
            }
        }
    }

    public function tokenTacoVendedor($user_id)
    {
        //\Log::debug('Obtener token user taco');
        $user = explode('|', $user_id);
        //\Log::debug($user);
        $taco_user_id = $user[0];
        $taco_empresa_id = $user[1];

        $APIKEY = env('TACO_API_KEY','_EMPTY_');
        $HOST = env('TACO_API_URL_PROD','_EMPTY_');
        $URL_API = $HOST.'/token_inventario';


        if($APIKEY!='_EMPTY_' && !empty($taco_user_id)){
            $api = new ClientApi();
            $options = [
                'form_params'=> [
                    'usuario_id'=> $taco_user_id,
                    'comercio_id'=> $taco_empresa_id,
                    'token'=> $APIKEY
                ]
            ];
            $response = $api->request('POST',$URL_API, $options);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data_user = json_decode($body, true);

            if(!isset($data_user['message'])){
                if(!empty($data_user)){
                    session(['taco_data_user' => $data_user]);
                    $this->taco_data_user = $data_user;
                    \Log::debug($data_user);
                    return true;
                }
            }
        }
        return false;
    }


    public function EmitirBoletaTest(){
        return [ 'Api emitir boleta CAMBIO',  $this->taco_data_user, $this->host_api];
    }

    private function getOptionsRequest($form_data){
        \Log::debug('FORM_PARAMS');
        \Log::debug($form_data);
        return [
            //'connect_timeout' => 30,
            //'timeout' => 59,
            'form_params'=> $form_data,
            'headers'=> [
                'Authorization' => 'Bearer ' . $this->taco_data_user['access_token']['token'],
                'Accept'        => 'application/json',
            ],
        ];
    }
    private function setItemProducto($NmbItem, $QtyItem, $PrcItem, $IndExe=''){
        $item = [
            'NmbItem'=> $NmbItem,
            'QtyItem'=> $QtyItem,
            'PrcItem'=> $PrcItem
        ];
        if(!empty($IndExe)){
            $item['IndExe'] = ''.$IndExe;
        }
        return $item;
    }

    public function prepareData($payment, $user){
        $venta_aux = [
            'venta_id'=> $payment->id,
            'vendedor'=> $user['name'],
            'cliente'=> (object)[
                'rut'=> '66666666-6',
                'nombre'=> 'Predeterminado',
                'glosa'=> '',
                'direccion'=> '',
                'comuna'=> '',
                'email'=> '',
            ],
            'total'=> $payment->consumption,
            'propina'=> $payment->tip,
            'observacion'=> '',
        ];
        $venta_items_aux = [];
        //\Log::debug('$payment->items');
        //\Log::debug($payment);
        //\Log::debug($payment->items);
        foreach ($payment->items as $item) {
            $venta_items_aux[] = [
                'producto_id'=> $item->product_id,
                'descripcion'=> $item->product->name,
                'cantidad'=> $item->order_count,
                'precio_venta'=> $item->product->sale_price,
                'es_exento'=> false,
            ];
        }
        $this->venta = $venta_aux;
        $this->venta_items = $venta_items_aux;
    }
    public function EmitirBoleta(){
        \Log::debug('aqui se hace la boleta');
        $venta = $this->venta;
        $venta_items = $this->venta_items;
        $items_productos = [];
        //\Log::debug($venta);
        //\Log::debug($venta_items);
        foreach ($venta_items as $item) {
            //\Log::debug($item);
            if($item['es_exento']){
                $items_productos[] = $this->setItemProducto($item['descripcion'],''.$item['cantidad'], ''.explode('.',$item['precio_venta'])[0], $es_exento='1');
            }else{
                $items_productos[] = $this->setItemProducto($item['descripcion'],''.$item['cantidad'], ''.explode('.',$item['precio_venta'])[0]);
            }
        }
        if(!empty($venta['propina'])){
            $items_productos[] = $this->setItemProducto('Propina','1', ''.explode('.',$venta['propina'])[0], $es_exento='2');
        }

        $taco_data_user = $this->taco_data_user;
        //\Log::debug('taco_data_user');
        //\Log::debug($taco_data_user);
        $data_documento = ['sin_iniciar'=>$taco_data_user];
        if(!empty($taco_data_user)){

            $usuario_comercio = $taco_data_user['usuario_comercio'];
            $comercio         = $taco_data_user['comercio'];
            $token_api        = $taco_data_user['access_token']['token'];

            $monto = ''.explode('.', $venta['total'])[0];
            $rut_comercio         = $comercio['rut_contribuyente'];//"6.362.194-3";
            $rut_usuario_comercio = $usuario_comercio['rut'];
            $medio_de_pago        = "1";
            $info_medio_pago      = "";


            $detalle = $items_productos;

            $rut_receptor = $venta['cliente']->rut;//"66.666.666-6";
            $razon_social_receptor = $venta['cliente']->nombre;
            $giro_receptor = $venta['cliente']->glosa;
            $direccion_receptor = $venta['cliente']->direccion;
            $comuna_receptor = $venta['cliente']->comuna;
            $correo_receptor = $venta['cliente']->email;

            $observacion      = $venta['observacion'];

            $data = [
                "rut_comercio"=> $rut_comercio,
                "rut_usuario_comercio"=> $rut_usuario_comercio,
                "monto"=> $monto,
                "medio_de_pago"=> $medio_de_pago,
                "info_medio_pago"=> $info_medio_pago,
                "detalle"=> json_encode($detalle),
                "rut_receptor"=> $rut_receptor,
                "razon_social_receptor"=> $razon_social_receptor,
                "giro_receptor"=> $giro_receptor,
                "direccion_receptor"=> $direccion_receptor,
                "comuna_receptor"=> $comuna_receptor,
                "correo_receptor"=> $correo_receptor,
                "observacion"=> $observacion
            ];

            if($rut_receptor=='66666666-6'){
                unset($data['rut_receptor']);
            }

            $URL_API = $this->host_api.'/dte/boleta/emitir';
            try {
                $api = new ClientApi();

                $options = $this->getOptionsRequest($data);

                $response       = $api->request('POST',$URL_API, $options);
                $statusCode     = $response->getStatusCode();
                $body           = $response->getBody()->getContents();
                $data_documento = json_decode($body, true);

                \Log::debug($data_documento);

                if(isset($data_documento['codigo']) && $data_documento['codigo']==200){

                }else{
                    $data_documento = ['sin_iniciar'=>$taco_data_user,'error'=>$data_documento['response']];
                }

            } catch (ConnectException $e) {
                //\Log::debug('ConnectException');
                //\Log::debug($e);
            }



            //\Log::debug('RESPUESTA TACO-BOLETA');
            //\Log::debug($data_documento);
            //dd($data_documento);
            $data_documento['venta_id'] = $venta['venta_id'];
            if(!isset($data_documento['message'])){
                return $data_documento;
            }else{
                return $data_documento;
            }
        }
        return $data_documento;

    }

    public function EmitirBoletaExenta($venta, $venta_items){
        $items_productos = [];
        foreach ($venta_items as $item) {
            $items_productos[] = $this->setItemProducto($item['descripcion'],''.$item['cantidad'], ''.explode('.',$item['precio_venta'])[0],'1');
        }


        $taco_data_user = $this->taco_data_user;
        $data_documento = ['sin_iniciar'=>$taco_data_user];
        if(!empty($taco_data_user)){

            $usuario_comercio = $taco_data_user['usuario_comercio'];
            $comercio = $taco_data_user['comercio'];
            $token_api = $taco_data_user['access_token']['token'];

            $monto = ''.explode('.', $venta['total'])[0];
            $rut_comercio = $comercio['rut_contribuyente'];//"6.362.194-3";
            $medio_de_pago = "1";
            $rut_usuario_comercio = $usuario_comercio['rut'];
            $info_medio_pago = "";

            $rut_receptor = $venta['cliente']->rut;//"66.666.666-6";
            $razon_social_receptor = $venta['cliente']->nombre;
            $giro_receptor = $venta['cliente']->glosa;
            $direccion_receptor = $venta['cliente']->direccion;
            $comuna_receptor = $venta['cliente']->comuna;
            $correo_receptor = $venta['cliente']->email;
            $detalle = $items_productos;
            $observacion      = $venta['observacion'];

            $data = [
                'rut_comercio'=> $comercio['rut_contribuyente'],
                'monto'=> $monto,
                "rut_comercio"=> $rut_comercio,
                "monto"=> $monto,
                "medio_de_pago"=> $medio_de_pago,
                "rut_usuario_comercio"=> $rut_usuario_comercio,
                "info_medio_pago"=> $info_medio_pago,
                "detalle"=> json_encode($detalle),
                "rut_receptor"=> $rut_receptor,
                "razon_social_receptor"=> $razon_social_receptor,
                "giro_receptor"=> $giro_receptor,
                "direccion_receptor"=> $direccion_receptor,
                "comuna_receptor"=> $comuna_receptor,
                "correo_receptor"=> $correo_receptor,
                "observacion"=> $observacion,
            ];

            ///return $data;

            //$HOST = env('TACO_API_URL_PROD','_EMPTY_');
            $URL_API = $this->host_api.'/dte/boleta/emitir_boleta_exenta';
            $api = new ClientApi();

            $options = $this->getOptionsRequest($data);

            $response = $api->request('POST',$URL_API, $options);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data_documento = json_decode($body, true);
            \Log::debug($data_documento);

            if(isset($data_documento['codigo']) && $data_documento['codigo']==200){

            }else{
                $data_documento = ['sin_iniciar'=>$taco_data_user,'error'=>$data_documento['response']];
            }
            //\Log::debug('RESPUESTA TACO-BOLETA-EXENTA');
            //\Log::debug($data_documento);
            $data_documento['venta_id'] = $venta['venta_id'];

            if(!isset($data_documento['message'])){
                return $data_documento;
            }else{
                return $data_documento;
            }
        }
        return $data_documento;

    }

    /**
     * Obtener todos los usuarios vendedores y admin, para poder asociar a los usuarios del inventario con taco
     */
    public function getUsuariosTaco($tipo = 'vendedores'){
        set_time_limit(0);
        $comercios = $this->getComerciosTaco();
        //\Log::debug($comercios);
        $APIKEY = env('TACO_API_KEY','_EMPTY_');
        $HOST = env('TACO_API_URL','_EMPTY_');
        $ACTION = $tipo;
        $URL_API = $HOST.'/migrate_data/'.$APIKEY.'/'.$ACTION;
        $data_usuario = [];
        $data_usuario[''] = '-- Seleccione Usuario TACO --';
        $data_usuario['0|0'] = 'SOLO IMPRIME RECIBO, NO REALIZA EMISIÓN DE DTE';

        if($APIKEY!='_EMPTY_'){
            $api = new ClientApi();
            $response = $api->request('GET',$URL_API);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $usuarios = json_decode($body, true);

            if(!isset($usuarios['error'])){
                if(!empty($usuarios)){
                    foreach ($usuarios as $key => $usuario) {
                        if($usuario['habilitado']==1){
                            $rut = str_replace(".", "", trim($usuario['rut']));
                            $nombre = trim($usuario['nombre']);
                            $nombre = str_replace("\n", "|", $nombre);
                            $nombre = str_replace("\r", "|", $nombre);
                            $nombre = str_replace("*", "", $nombre);
                            $comercio = isset($comercios[$usuario['comercio_id']]) ? $comercios[$usuario['comercio_id']] : 'NO-EXISTE-EMPRESA';
                            $data_usuario[$usuario['id'].'|'.$usuario['comercio_id']] = implode(' | ',[$rut,$nombre,$usuario['numero'],$comercio]);
                            //\Log::debug($usuario);
                        }
                    }
                }
            }
        }
        //\Log::debug($data_usuario);
        return $data_usuario;
    }

    public function getComerciosTaco(){
        set_time_limit(0);
        $APIKEY = env('TACO_API_KEY','_EMPTY_');
        $HOST = env('TACO_API_URL','_EMPTY_');
        $ACTION = 'comercios';
        $URL_API = $HOST.'/migrate_data/'.$APIKEY.'/'.$ACTION;
        $data_comercios = [];
        if($APIKEY!='_EMPTY_'){
            $api = new ClientApi();
            $response = $api->request('GET',$URL_API);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $comercios = json_decode($body, true);

            if(!isset($comercios['error'])){
                if(!empty($comercios)){
                    foreach ($comercios as $key => $comercio) {
                        if($comercio['habilitado']==1){
                            $rut = str_replace(".", "", trim($comercio['rut_contribuyente']));
                            $nombre = trim($comercio['nombre']);
                            $nombre = str_replace("\n", "|", $nombre);
                            $nombre = str_replace("\r", "|", $nombre);
                            $nombre = str_replace("*", "", $nombre);
                            $data_comercios[$comercio['id']] = '['.$rut.'] '.$nombre;
                            //\Log::debug($comercio);
                        }
                    }
                }
            }
        }
        return $data_comercios;
    }

}