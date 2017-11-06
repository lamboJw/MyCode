// Copyright (c) 2014 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

/**
 * Get the current URL.
 *
 * @param {function(string)} callback called when the URL of the current tab
 *   is found.
 */
function getCurrentTabUrl(callback) {
  // Query filter to be passed to chrome.tabs.query - see
  // https://developer.chrome.com/extensions/tabs#method-query
  var queryInfo = {
    active: true,
    currentWindow: true
  };

    //chrome.tabs.query(queryInfo, (tabs) = > {程序片});这样写可以直接调用本方法内的参数（如此处的callback），function (tabs){}这样写无效
  chrome.tabs.query(queryInfo, (tabs) => {
    // chrome.tabs.query invokes the callback with a list of tabs that match the
    // query. When the popup is opened, there is certainly a window and at least
    // one tab, so we can safely assume that |tabs| is a non-empty array.
    // A window can only have one active tab at a time, so the array consists of
    // exactly one tab.
    // 查询当前页面的弹出框，一个页面只允许有一个弹出框，所以直接取第0个
    var tab = tabs[0];

    // A tab is a plain object that provides information about the tab.
    // See https://developer.chrome.com/extensions/tabs#type-Tab
    // 弹出框包含一堆属性，其中url表示弹出框所在页面的url
    var url = tab.url;

    // tab.url is only available if the "activeTab" permission is declared.
    // If you want to see the URL of other tabs (e.g. after removing active:true
    // from |queryInfo|), then the "tabs" permission is required to see their
    // "url" properties.
    // 保证url的类型为string，console.assert是断言，参数一为正确的条件，参数二为错误时打印出来的信息
    console.assert(typeof url == 'string', 'tab.url should be a string');
    //调用回调函数
    callback(url);
  });

  // 大多数chrome的扩展程序Api都是异步的. 这代表不能出现以下的程序:
  //
  // var url;
  // chrome.tabs.query(queryInfo, (tabs) => {
  //   url = tabs[0].url;
  // });
  // alert(url); // 显示 "undefined", 因为 chrome.tabs.query 是异步的.
}

/**
 * Change the background color of the current page.
 *
 * @param {string} color The new background color.
 */
function changeBackgroundColor(color) {
  var script = 'document.body.style.backgroundColor="' + color + '";';
  // See https://developer.chrome.com/extensions/tabs#method-executeScript.
  // chrome.tabs.executeScript  允许我们用编程的方式向当前页面注入js. 当我们省略了第一个可选参数"tabid"时会直接作用在当前窗口
  chrome.tabs.executeScript({
    code: script
  });
  //在第二个参数details中，code和file至少要有一个，但不需要两个同时出现
}

/**
 * Gets the saved background color for url.
 *
 * @param {string} url URL whose background color is to be retrieved.
 * @param {function(string)} callback called with the saved background color for
 *     the given url on success, or a falsy value if no color is retrieved.
 */
function getSavedBackgroundColor(url, callback) {
  // See https://developer.chrome.com/apps/storage#type-StorageArea. We check
  // for chrome.runtime.lastError to ensure correctness even when the API call
  // fails.
  // 调取之前保存的记录中的信息,参数一未key，参数二为回调函数
  chrome.storage.sync.get(url, (items) => {
    callback(chrome.runtime.lastError ? null : items[url]);
  });
  //chrome.runtime.lastError 当Api调用出错时，会定义这样一个东西
}

/**
 * Sets the given background color for url.
 *
 * @param {string} url URL for which background color is to be saved.
 * @param {string} color The background color to be saved.
 */
function saveBackgroundColor(url, color) {
  var items = {};
  items[url] = color;
  // See https://developer.chrome.com/apps/storage#type-StorageArea. We omit the
  // optional callback since we don't need to perform any action once the
  // background color is saved.
  chrome.storage.sync.set(items);
  //保存一个键值对在storage中
}

// This extension loads the saved background color for the current tab if one
// exists. The user can select a new background color from the dropdown for the
// current page, and it will be saved as part of the extension's isolated
// storage. The chrome.storage API is used for this purpose. This is different
// from the window.localStorage API, which is synchronous and stores data bound
// to a document's origin. Also, using chrome.storage.sync instead of
// chrome.storage.local allows the extension data to be synced across multiple
// user devices.
document.addEventListener('DOMContentLoaded', () => {
  getCurrentTabUrl((url) => {
    var dropdown = document.getElementById('dropdown');

    // Load the saved background color for this page and modify the dropdown
    // value, if needed.
    //如果有保存的背景颜色，则直接读取，用于刷新后变颜色
    getSavedBackgroundColor(url, (savedColor) => {
      if (savedColor) {
        changeBackgroundColor(savedColor);
        dropdown.value = savedColor;
      }
    });

    // Ensure the background color is changed and saved when the dropdown
    // selection changes.
    dropdown.addEventListener('change', () => {
      changeBackgroundColor(dropdown.value);
      saveBackgroundColor(url, dropdown.value);
    });
  });
});
