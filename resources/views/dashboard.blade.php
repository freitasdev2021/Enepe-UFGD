<x-educacional-layout>
    @if(in_array(Auth::user()->tipo,[0,1]))
     <div class="shadow p-3 dashboard">
        <div class="col-sm-12 row">
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-buildings' ></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Secretarías Ativas</span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-school'></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Escolas </span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bx-child'></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Alunos </span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-pencil'></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Professores </span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-briefcase'></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Diretores </span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
           <div class="col-sm-4">
              <div class="info-box">
                 <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-user'></i></span>
                 <div class="info-box-content">
                    <span class="info-box-text">Usuários</span>
                    <span class="info-box-number">
                    0
                    </span>
                 </div>
              </div>
           </div>
        </div>
     </div>
     <script src="{{asset('js/relatoriosfornecedor.js')}}"></script>
     @elseif(in_array(Auth::user()->tipo,[0,2]))
     <div class="shadow p-3 dashboard">
       <div class="col-sm-12 row">
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-buildings' ></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Matriculas</span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
          <div class="col-sm-4">
             <div class="info-box">
                <span class="info-box-icon bg-fr elevation-1"><i class='bx bxs-user'></i></span>
                <div class="info-box-content">
                   <span class="info-box-text">Usuários</span>
                   <span class="info-box-number">
                   0
                   </span>
                </div>
             </div>
          </div>
       </div>
    </div>
     @elseif(in_array(Auth::user()->tipo,[0,3]))
     <div class="shadow">
       {{-- <pre>
          {{print_r($ficha)}}
       </pre> --}}
       
     </div>
     @endif
  </x-educacional-layout>