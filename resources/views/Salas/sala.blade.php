<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Meeting</title>
    <script type="module">
        import { defineCustomElements } from 'https://cdn.jsdelivr.net/npm/@dytesdk/ui-kit@2.0.0/loader/index.es2017.js';
        defineCustomElements();
    </script>
    <!-- Import Web Core via CDN too -->
    <script src="https://cdn.dyte.in/core/dyte-2.0.3.js"></script>
    <script type="module">
        import {
          provideDyteDesignSystem, extendConfig,
        } from 'https://cdn.jsdelivr.net/npm/@dytesdk/ui-kit@2.0.0/dist/esm/index.js';
      </script>
</head>
<body>
    <dyte-meeting id="my-meeting"></dyte-meeting>
    <script>
  
      const init = async () => {
        // Fetch auth token from server side
        const response = await fetch("/Atividades/Abrir?meeting_id={{$Sala->IDMeeting}}&name={{$Nome}}");
        const data = await response.json();
        // console.log(data)
        // return false
        const authToken = data.data.token;
  
        const meeting = await DyteClient.init({
          authToken,
          defaults: {
            audio: true,
            video: true,
          },
        });
  
        document.getElementById('my-meeting').meeting = meeting;
      };
  
      init();
    </script>
  </body>
</html>
