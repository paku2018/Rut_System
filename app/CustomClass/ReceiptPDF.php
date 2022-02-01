<?php
namespace App\CustomClass;

/**
 *
 */
class ReceiptPDF
{
	public $pdf = null;
	public $total = 0;

	function __construct()
	{
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(-1, 0, 0, true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(TRUE, 4);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $this->pdf = $pdf;
	}

	public function getStyles($html = ''){
		$fonts = [10,12,14,16,18];
		$aligns = ['right','center','left'];
		$colors = ['blue','green','red'];
		$style = '
            <style>
                .bold{ font-weight: 600; }
                .common-table{
                    padding: 0px !important;
                    width:48mm;
                }
                .v-bottom{ vertical-align: bottom; }
                .mb-0{ margin-bottom: 0 !important; }
                .mt-0{ margin-top: 0 !important; }
                .text-grey{ color: #939393; }
                .py-0{
                    padding-bottom: 0 !important;
                    padding-top: 0 !important;
                }
                .px-0{
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .mt-1{  margin-top: 10px !important; }
                .ml-3{  margin-left: 20px !important; }
                .bb-1{ border-bottom: 1px solid #eaeaea; }
                table{ font-family: Noto Sans TC; }
                .bb { border: 1px #000000 solid; padding: 0px;}
                p { font-family: Arial;  }

                ';

            foreach($fonts as $size){
            	$style .= '.font-'.$size.'{ font-size: '.$size.'px !important;  }';
            }
            foreach($aligns as $align){
            	$style .= '.text-'.$align.'{text-align: '.$align.' !important;  }';
            }
            foreach($colors as $color){
            	$style .= '.text-'.$color.'{color: '.$color.';  }';
            }
            $style .= '</style>';
		return $style.$html;
	}

	public function footer(){
		$html = '<table class="common-table"><tbody>
                        <tr>
                            <th class="text-center">
                                <p class="font-weight-bold">www.controlcash.cl</p>
                                <p class="font-weight-bold">Gracias por su compra</p>
                            </th>
                        </tr>
                    </tbody>
                </table>';
		return $html;
	}
	public function setComment($comment){
		if(empty($comment)){
			return '';
		}
		$html = '<table class="common-table">
                    <tbody>
                        <tr>
                            <td class="font-10"><br><br>Comentario:<br>'.$comment.'</td>
                        </tr>
                    </tbody>
            </table>';
        return $html;
	}
	public function setTotal($total=0){
		$html = '<table class="common-table">
                    <tbody>
                        <tr>
                            <th class="text-left bb-1 text-right"><h3>TOTAL : $'.number_format($total, 0) .'</h3></th>
                        </tr>
                    </tbody>
            </table>';
        return $html;
	}
	public function setPaymentDetails($type, $pay = 0,$return = 0){
		 $html = '<table class="common-table">';
         $html .= '<tbody>
                        <tr>
                            <th class="text-left bb-1">
                                <p class="bold font-14">Tipo de pago:<br> <span id="tipo">'.$type.'</span></p>
                                <p class="bold font-14">Pago : <span>$'.number_format($pay, 0) .'</span></p>
                                <p class="bold font-14">Vuelto : <span>$'.number_format($return, 0) .'</span></p>
                            </th>
                        </tr>
                    </tbody>';
        $html = '</table>';
        return $html;
	}

	public function setItems($orders){
		$html = '<br><br><table class="common-table">
                <tbody>';

        $this->total = 0;
        if($orders){
        	foreach ($orders as $order){
	            $price = $order->product->sale_price * $order->order_count;
	            $this->total += $price;
	            $html .= '<tr>';
	            $html .= '<td class="text-left font-10">'. $order->product->name .' <br>'.$order->order_count.' &times; $'.$order->product->sale_price.'</td>';
	            $html .= '<td class="text-right font-12"><br>$'. number_format($price, 0) .'<br><br></td>';
	            $html .= '</tr>';
	        }
        }

        $html .= '</tbody></table>';
        return $html;
	}

	public function renderDetail(){
		$html =  '<table class="common-table">
                <tbody>
                    <tr>
                        <td class="text-center" style="border-bottom: 1px dashed grey;"><br><br>D E T A L L E<br></td>
                    </tr>
                </tbody>
            </table>';
        return $html;
	}

	public function renderHeader($data){
		$html = '
	        <table class="common-table">
		        <tbody>
		        <tr>
		            <td colspan="2" class="text-center">
		                <h5>'.__('receipt').'</h5>
		                <h4>N°: '.$data['order_code'].'</h4>
		                <p class="font-12">Fecha: <span id="current_time">'.date('d/m/Y H:i').'</span></p>
		                <p class="font-12 bold">'.$data['table']->restaurant->name.'</p>
		                <p class="font-12">Rut: '.$data['table']->restaurant->rut.'</p>
		                <p class="font-12">M: '.$data['table']->name.'</p>
		            </td>
		        </tr>
		        </tbody>
	        </table>';
	    return $html;
	}
}