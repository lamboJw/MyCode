chrome.runtime.onConnect.addListener(function(port) {
    console.assert(port.name == "url");
    port.onMessage.addListener(function(msg) {
        if (msg.url != "")
            alert(msg.url);
    });
    port.postMessage({msg: "connect_ok"});//发送消息
});

document.addEventListener('DOMContentLoaded',function(){
    chrome.tabs.query({active:true, currentWindow: true, windowId:-2}, function(tabs){
        var tab = tabs[0];
        chrome.tabs.executeScript(tab.id, {file:'js/content.js'});
    });
});