<x-educacional-layout>
    @if(in_array(Auth::user()->tipo,[0,1]))
     <div class="shadow p-3 dashboard">
        
     </div>
     <script src="{{asset('js/relatoriosfornecedor.js')}}"></script>
     @elseif(in_array(Auth::user()->tipo,[0,2]))
     <div class="shadow p-3 dashboard">
       
    </div>
     @elseif(in_array(Auth::user()->tipo,[0,3]))
     <div class="shadow">
       
     </div>
     @endif
  </x-educacional-layout>