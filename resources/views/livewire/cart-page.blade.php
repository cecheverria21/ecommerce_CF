<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="container mx-auto px-4">
      <h1 class="text-2xl font-semibold mb-4">Carro de la compra</h1>
      <div class="flex flex-col md:flex-row gap-4">
        <div class="md:w-3/4">
          <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
            <table class="w-full">
              <thead>
                <tr>
                  <th class="text-left font-semibold">Producto</th>
                  <th class="text-left font-semibold">Precio</th>
                  <th class="text-left font-semibold">Cantidad</th>
                  <th class="text-left font-semibold">Total</th>
                  <th class="text-left font-semibold">Eliminar</th>
                </tr>
              </thead>
              <tbody>

                
                

                @forelse ($cart_items as $item)
                
                  <tr wire:key=' {{ $item['product_id'] }} '>
                    <td class="py-4">
                      <div class="flex items-center">
                        <img class="h-16 w-16 mr-4" src="{{ url('storage', $item['image']) }}" alt=" {{ $item['name'] }} ">
                        <span class="font-semibold">{{ $item['name'] }}</span>
                      </div>
                    </td>
                    <td class="py-4">
                      {{ Number::currency($item['unit_amount']) }}
                    </td>
                    <td class="py-4">
                      <div class="flex items-center">
                        <button wire:click='decreaseQty( {{ $item['product_id'] }} )' class="border rounded-md py-2 px-4 mr-2">-</button>
                        <span class="text-center w-8"> {{ $item['quantity'] }} </span>
                        <button wire:click='increaseQty( {{ $item['product_id'] }} )' class="border rounded-md py-2 px-4 ml-2">+</button>
                      </div>
                    </td>
                    <td class="py-4">
                      {{ Number::currency($item['total_amount']) }}
                    </td>
                    <td><button  wire:click='removeItem({{ $item['product_id'] }})' class="bg-slate-300 border-2 border-slate-400 rounded-lg px-3 py-1 hover:bg-red-500 hover:text-white hover:border-red-700"><span wire:loading.remove wire:target='removeItem({{ $item['product_id'] }})'>Eliminar</span><span wire:loading wire:target='removeItem({{ $item['product_id'] }})'>Quitando...</span></button></td>
                  </tr>                

                @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-4xl font-semibold text-slate-500" >No items available in cart!</td>
                    </tr>
                @endforelse



                <!-- More product rows -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="md:w-1/4">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Resumen</h2>
            <div class="flex justify-between mb-2">
              <span>Subtotal</span>
              <span>{{ Number::currency($grand_total) }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span>Impuestos</span>
              <span>{{ Number::currency(0) }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span>Envío</span>
              <span>{{ Number::currency(0) }}</span>
            </div>
            <hr class="my-2">
            <div class="flex justify-between mb-2">
              <span class="font-semibold">Gran total</span>
              <span class="font-semibold">{{ Number::currency($grand_total) }}</span>
            </div>
            @if ($cart_items)
              <a href="/checkout" class="bg-blue-500 block text-center text-white py-2 px-4 rounded-lg mt-4 w-full">Verificar</a>    
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
