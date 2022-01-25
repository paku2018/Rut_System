<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //DTE
    public const BOLETA        = 39; //Boleta electrónica
    public const BOLETA_EXENTA = 41; //Boleta exenta electrónica
    public const DTE_CON_IVA = ['33','52'];
    public const IVA = 19;

    //SIN DTE
    public const RECIBO_LOCAL  = 0;

    public static function getTypes():array{
    	return [
			SELF::RECIBO_LOCAL => 'Recibo',
			SELF::BOLETA => 'Boleta electrónica',
    	];
    }

    public static function getCalculations($total, $dte, $es_exento = false){
        $sub_total = 0.00;
        $monto_iva = 0.00;
        $iva = !$es_exento ? SELF::IVA.'%' : '0%';
        $es_con_iva = false;
        if(!$es_exento){
            if(in_array($dte, SELF::DTE_CON_IVA)){
                $sub_total = $total / ((SELF::IVA/100) + 1);
                $monto_iva = $total - $sub_total;
                $sub_total = round($sub_total, 0);
                $monto_iva = round($monto_iva, 0);
                $es_con_iva = true;
            }else if((int) $dte == SELF::BOLETA){
                $sub_total2 = $total / ((SELF::IVA/100) + 1);
                $monto_iva = $total - $sub_total2;
                $monto_iva = round($monto_iva, 0);
            }
        }

        return compact('sub_total','monto_iva','iva','es_con_iva');
    }
}
