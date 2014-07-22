Order No {$mail.order.id} was cancelled

<br><br>
{if $mail.order.order_for == 'mam'}
You can manage your orders at <a href="http://myroom.steelemotion.com/myroom/orders">"My Room" - "Orders"</a>
{elseif $mail.order.order_for == 'pa'}
You can manage your orders at <a href="http://myroom.platesahead.com/myroom/orders">"My Room" - "Orders"</a>
{/if}

<br><br>
With best regards,<br> 
<b>{if $mail.order.order_for == 'mam'}M -a- M{elseif $mail.order.order_for == 'pa'}PlatesAhead{/if}</b>
