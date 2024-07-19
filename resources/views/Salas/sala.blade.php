<div class="row chats">
    <div class="shadow video chat" id="meetingSDKElement">
        
    </div>
    <div class="shadow conversa chat">
        <h1>Chat</h1>
    </div>
</div>
<input type="hidden" name="IDMeeting" value="{{$Sala->IDMeeting}}">
<input type="hidden" name="PWMeeting" value="{{$Sala->PWMeeting}}">
<input type="hidden" name="APIKey" value="5r5RG6czTv6vBc0Mw4YEJQ">
<input type="hidden" name="APISecret" value="2ssi5eF8AOclxl7DQmCsHqoF1OC49E7N">
<input type="hidden" name="userName" value="{{$Nome}}">
<input type="hidden" name="userEmail" value="{{$Email}}">
<script src="https://source.zoom.us/3.8.0/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/3.8.0/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/3.8.0/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/3.8.0/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/3.8.0/lib/vendor/lodash.min.js"></script>
<script src="https://source.zoom.us/3.8.0/zoom-meeting-3.8.0.min.js"></script>
<script src="{{asset('js/tool.js')}}"></script>
<script src="{{asset('js/vconsole.min.js')}}"></script>
<script src="{{asset('js/meeting.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>