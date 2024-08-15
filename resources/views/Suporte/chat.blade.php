<style>
    #chat-container {
        width: 100%;
        height:50dvh;
        overflow-y:scroll;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    #chat-box {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        border-bottom: 1px solid #ccc;
    }

    #chat-form {
        display: flex;
        padding: 10px;
    }

    #message-input {
        flex: 1;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-right: 5px;
    }

    button {
        padding: 5px 10px;
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

</style>
<x-educacional-layout>
    <div class="card">
        <div class="card-header bg-fr text-white">
            Suporte <a href='https://wa.me/5531983086235' class="btn btn-xs btn-success" target="_blank">Whatsapp</a> <a class="btn btn-xs btn-success">maxhenrique308@gmail.com</a> <a class="btn btn-xs btn-success">31 9 83086235</a>
        </div>
        <div class="card-body row">
            <div id="chat-container">
                <div id="chat-box"></div>
                <div id="chatContainer">
                    
                </div>
                <form id="chat-form">
                    <input type="text" name="Mensagem" id="message-input" placeholder="Digite sua mensagem...">
                    <input type="hidden" name="IDConversa" value="{{$id}}">
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
     </div>
</x-educacional-layout>
<script>
    $(document).ready(function(){
        //FUNÇÃO DE ENVIAR MENSAGEM
        $("#chat-form").on("submit",function(event){
            event.preventDefault()
            $.ajax({
                url : '{{route('Suporte/Enviar')}}',
                method : "POST",
                data : {
                    IDConversa : $("input[name=IDConversa]").val(),
                    Mensagem : $("input[name=Mensagem]").val()
                },
                headers : {
                    'X-CSRF-TOKEN' : '{{csrf_token()}}'
                }
            }).done(function(resp){
                $("input[name=Mensagem]").val("")
                console.log(resp)
            })    
        })
        //FUNÇÃO DE RECEBER A MENSAGEM
        function receberMensagens(){
            $.ajax({
                url : '{{route('Suporte/Receber',$id)}}',
                method : "GET"
            }).done(function(resp){
                $("#chatContainer").html(resp)
            })    
        }
        setInterval(receberMensagens,300)
        //
    })
</script>