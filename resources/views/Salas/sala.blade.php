<x-educacional-layout>
    <div class="row chats">
        <div class="shadow video chat">
            <div id="zmmtg-root meet"></div>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/react.min.js"></script>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/react-dom.min.js"></script>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/redux.min.js"></script>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/redux-thunk.min.js"></script>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/jquery.min.js"></script>
            <script src="https://source.zoom.us/2.9.7/lib/vendor/lodash.min.js"></script>
            <script src="https://source.zoom.us/zoom-meeting-2.9.7.min.js"></script>

            <script>
                ZoomMtg.setZoomJSLib('https://source.zoom.us/2.9.7/lib', '/av');

                ZoomMtg.preLoadWasm();
                ZoomMtg.prepareJssdk();

                const meetingNumber = '{{$Sala->IDMeeting}}'; // Número da reunião Zoom
                const userName = '{{$Nome}}'; // Nome do usuário participante
                const appKey = '{{env('ZOOM_CLIENT_ID')}}';
                const userEmail = '{{$Email}}'; // Email do usuário (opcional)
               
                const leaveUrl = 'http://127.0.0.1:8000/Atividades'; // URL para redirecionar ao sair da reunião
                const signatureEndpoint = '/zoom/signature';
                console.log('Iniciando processo de ingresso na reunião...');
                fetch(signatureEndpoint + '?meetingNumber=' + meetingNumber)
                .then(res => res.json())
                .then(data => {
                    const { signature } = data;
                    console.log('Assinatura obtida com sucesso:', signature);

                    ZoomMtg.init({
                    leaveUrl: leaveUrl,
                    success: function () {
                        console.log('Zoom SDK inicializado com sucesso.');
                        console.log(meetingNumber)
                        console.log(userName)
                        console.log(signature)
                        console.log(appKey)
                        console.log(userEmail)
                        ZoomMtg.join({
                        meetingNumber: meetingNumber,
                        userName: userName,
                        signature: signature,
                        appKey: appKey,
                        userEmail: userEmail,
                        
                        success: function (res) {
                            console.log('Seja Bem Vindo: '+res);
                        },
                        error: function (res) {
                            console.log("deu errado: ".res);
                        }
                        });
                    },
                    error: function (res) {
                        console.log(res);
                    }
                    });
                })
                .catch(err => console.error('Failed to fetch signature:', err));
            </script>
        </div>
        <div class="shadow conversa chat">
            <h1>Chat</h1>
        </div>
    </div>
</x-educacional-layout>