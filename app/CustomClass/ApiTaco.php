<?php
namespace App\CustomClass;

use GuzzleHttp\Client as ClientApi;
use GuzzleHttp\Exception\ConnectException;
use App\User;
use App\Models\Document;

/**
 * Clase para emision de boletas y facturas desde la api de Taco pagocash
 *
 * */
class ApiTaco {
    var $taco_data_user = [];
    var $host_api = '';

    public function __construct($user_id = null){
        $this->taco_data_user = session('taco_data_user', function() { return []; });
        $this->host_api = env('TACO_API_URL_PROD','_EMPTY_');

        if(!empty($user_id)){
            if(empty($this->taco_data_user)){
                $this->tokenTacoVendedor($user_id);
            }
        }
    }

    public function tokenTacoVendedor($user_id)
    {
        $user = User::find($user_id);

        $APIKEY = env('TACO_API_KEY','_EMPTY_');
        $HOST = env('TACO_API_URL_PROD','_EMPTY_');
        $URL_API = $HOST.'/token_inventario';


        if($APIKEY!='_EMPTY_' && !empty($user->taco_user_id)){
            $api = new ClientApi();
            $options = [
                'form_params'=> [
                    'usuario_id'=> $user->taco_user_id,
                    'comercio_id'=> 0,//$user->empresa->comercio_id,
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
                    //\Log::debug($data_user);
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
            $item['IndExe'] = '1';
        }
        return $item;
    }


    public function EmitirBoleta($venta, $venta_items){
        $items_productos = [];
        foreach ($venta_items as $item) {
            if($item['es_exento']){
                $items_productos[] = $this->setItemProducto($item['descripcion'],''.$item['cantidad'], ''.explode('.',$item['precio_venta'])[0], $es_exento='1');
            }else{
                $items_productos[] = $this->setItemProducto($item['descripcion'],''.$item['cantidad'], ''.explode('.',$item['precio_venta'])[0]);
            }
        }

        $taco_data_user = $this->taco_data_user;
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

            //\Log::debug('RUT RECEPTOR:'.$rut_receptor);

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