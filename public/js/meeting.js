window.addEventListener("DOMContentLoaded", function (event) {
  console.log("DOM fully loaded and parsed");
  websdkready();
});

function websdkready() {
  var testTool = window.testTool;
  // get meeting args from url
  var tmpArgs = testTool.parseQuery();
  var meetingConfig = {
    sdkKey: $("input[name=APIKey]").val(),
    meetingNumber: $("input[name=IDMeeting]").val(),
    userName: $("input[name=userName]").val(),
    leaveUrl: "http://127.0.0.1:8000/Atividades",
    role: 0,
    userEmail: $("input[name=userEmail]").val(),
    lang: tmpArgs.lang,
    china: tmpArgs.china === "1",
  };
  //
  // const signatureEndpoint = '/zoom/signature'
  // console.log('Iniciando processo de ingresso na reunião...');
  // fetch(signatureEndpoint + '?meetingNumber=' + $('input[name=IDMeeting]').val() + '&role=0')
  // .then(res => res.json())
  // .then(data => {
  //     const { signature } = data;
  //     meetingConfig.signature = signature
  // }).catch(err => console.error('Failed to fetch signature:', err));
  var signature = ZoomMtg.generateSDKSignature({
    meetingNumber: meetingConfig.meetingNumber,
    sdkKey: meetingConfig.sdkKey,
    sdkSecret: $("input[name=APISecret]").val(),
    role: 0,
    expiration: Math.round(new Date().getTime() / 1000) + 3600,
    success: function (res) {
      meetingConfig.signature = res
    },
  });

  console.log(signature)
  //
  // a tool use debug mobile device
  console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));

  // it's option if you want to change the MeetingSDK-Web dependency link resources. setZoomJSLib must be run at first
  // ZoomMtg.setZoomJSLib("https://source.zoom.us/{VERSION}/lib", "/av"); // default, don't need call it
  if (meetingConfig.china)
    ZoomMtg.setZoomJSLib("https://jssdk.zoomus.cn/3.8.0/lib", "/av"); // china cdn option

  ZoomMtg.preLoadWasm();
  ZoomMtg.prepareWebSDK();

  function beginJoin(signature) {
    ZoomMtg.i18n.load(meetingConfig.lang);
    ZoomMtg.init({
      leaveUrl: meetingConfig.leaveUrl,
      // disablePreview: false, // default false
      success: function () {
        console.log(meetingConfig);
        console.log("signature", signature);

        ZoomMtg.join({
          meetingNumber: meetingConfig.meetingNumber,
          userName: meetingConfig.userName,
          signature: signature,
          sdkKey: meetingConfig.sdkKey,
          userEmail: meetingConfig.userEmail,
          passWord : $("input[name=PWMeeting]").val(),
          success: function (res) {
            console.log("join meeting success");
            console.log("get attendeelist");
            ZoomMtg.getAttendeeslist({});
            ZoomMtg.getCurrentUser({
              success: function (res) {
                console.log("success getCurrentUser", res.result.currentUser);
              },
            });
          },
          error: function (res) {
            console.log(res);
          },
        });
      },
      error: function (res) {
        console.log(res);
      },
    });

    ZoomMtg.inMeetingServiceListener("onUserJoin", function (data) {
      console.log("inMeetingServiceListener onUserJoin", data);
    });

    ZoomMtg.inMeetingServiceListener("onUserLeave", function (data) {
      console.log("inMeetingServiceListener onUserLeave", data);
    });

    ZoomMtg.inMeetingServiceListener("onUserIsInWaitingRoom", function (data) {
      console.log("inMeetingServiceListener onUserIsInWaitingRoom", data);
    });

    ZoomMtg.inMeetingServiceListener("onMeetingStatus", function (data) {
      console.log("inMeetingServiceListener onMeetingStatus", data);
    });
  }

  beginJoin(meetingConfig.signature);
}
