<h2>Su pedido fue entregado con éxito.</h2>
Este correo electrónico es para la confirmación de la entrega de sus pedidos.
Los siguientes productos se entregan con éxito.
<br>
<br>
Productos entregados :
<br>
@foreach($detail['products'] as $product)
    - {{$product['product_name']}} : {{number_format($product['product_price'], 0, ".", ",")."*".$product['product_count']."=".number_format($product['product_price']*$product['product_count'], 0, ".", ",")}}<br>
@endforeach
<br>
Total : {{number_format($detail['total'], 0, ".", ",")}}
<br>
※Este correo electrónico es solo para enviar. Incluso si responde a este correo electrónico, no podremos responder. Tenga en cuenta.
<br>
<br>
<br>


