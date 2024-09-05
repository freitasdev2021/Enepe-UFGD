<!doctype html>
<html>
   <head>
      <meta charset='utf-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>FR Eventos Digitais</title>
      <link rel="icon" type="image/x-icon" href="{{asset('img/fricon.ico')}}" />
      <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
      <link rel="stylesheet" href="{{asset('css/snipets.css')}}">
      <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
      <link href="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.css" rel="stylesheet">
      <link href="{{asset('js/chartjs/chart.css')}}" rel="stylesheet">
      <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
      <script src="{{asset('js/inputmask.js')}}"></script>
   </head>
   @yield('content')
         <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
         <script src="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.js"></script>
         <script src="{{asset('js/datatablesGeral.js')}}"></script>
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
         <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- CDN do Chart.js -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
         <script>
            function convertJpgToPdf(caminho){
               const { jsPDF } = window.jspdf;
               // Definir o PDF com tamanho personalizado para 1920x1080 pixels em modo paisagem
               const pdf = new jsPDF({
                  orientation: 'landscape', // Orientação paisagem
                  unit: 'px', // Unidades em pixels
                  format: [1920, 1080], // Tamanho da página em pixels
               });
               
               const img = new Image();
               img.src = caminho; // Substitua pelo caminho da sua imagem

               img.onload = function() {
                  // Adicionar a imagem ao PDF e ajustá-la para ocupar toda a página
                  pdf.addImage(img, 'JPEG', 0, 0, 1920, 1080);
                  
                  // Salvar o PDF
                  pdf.save('certificado.pdf');
               };
            }
         </script>
</html>