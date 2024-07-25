<select name="modelo[]" data-inscrito="{{$IDInscrito}}">
    <option value="">Selecione um Modelo</option>
    @foreach($modelos as $m)
    <option value="{{$m->id}}" {{($modelo == $m->id) ? 'selected' : ''}}>{{$m->Nome}}</option>
    @endforeach
</select>
<script>
    $("select[name=modelo]").on("change",function(){
        alert("Ola")
        $(this).parents("tr").find("input[name=IDInscrito]").val($(this).val())
    })
</script>