var urlAtual = window.location.href;
const urlParams = new URLSearchParams(window.location.search);
//FILTRO DE MOVIMENTAÇÕES DE COLABORADORES
var partesDaUrlMovimentacoes = urlAtual.split('/');
var IDColaborador = partesDaUrlMovimentacoes[partesDaUrlMovimentacoes.length - 1];
//FILTRO DE COLABORADORES
if(urlParams.get('Tipo')){
    var tp = urlParams.get('Tipo')
    var ev = urlParams.get('evento')
    urlApontamentos = $("#escolas").attr("data-rota")+"?Tipo="+tp+"&evento="+ev
}else{
    urlApontamentos = $("#escolas").attr("data-rota")
}

console.log(urlApontamentos)

//

$("#secretarias").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : $("#secretarias").attr("data-rota")
});

$("#escolas").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : urlApontamentos
});

$("#secretariasAdministradores").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : $("#secretariasAdministradores").attr("data-rota")
});