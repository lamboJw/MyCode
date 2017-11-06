var port = chrome.runtime.connect({name: "url"});//通道名称
$("#btn").bind("click", function () {
    port.postMessage({url: $("#label1").text()});//发送消息
});

port.onMessage.addListener(function (msg) {//监听消息
    alert(msg.msg);
});

function getURL(){
    if(typeof($("#v1").attr('src'))=="undefined"){
        console.log("未找到url");
        setTimeout("getURL()",1000);
    }else{
        var url = $("#v1").attr('src');
        alert(url);
    }
}

getURL();