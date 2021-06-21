@component('mail::message')
# Congratiolations, your order has been placed!
## (Order number #{{$order->id}})
Order information:

@component('mail::button', ['url' => $url])
Show order
@endcomponent

@component('mail::table')
| Products    | Quantity   | Price  |
|:------:   |:-----------   |:--------: |
@foreach($order->order_details as $order_details)
| {{$order_details->product->name}}     | {{$order_details->quantity}} |        {{$order_details->product->price}} |
@endforeach
@endcomponent

Thanks for making use of our store,<br>
{{ config('app.name') }}
@endcomponent
