/* Public frontend enhancement. Server HTML is the no-JS fallback. */
(function () {
  'use strict';
  var nodes = document.querySelectorAll('.alpdt-live[data-alpdt-config][data-alpdt-endpoint]');
  function siteDay(zone) {
    try {
      return new Intl.DateTimeFormat('en-US', {timeZone:zone,year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
    } catch (err) { return new Date().toISOString().slice(0,10); }
  }
  var lastDays = new WeakMap();
  function refresh(node) {
    var params, url;
    try {
      params = JSON.parse(node.getAttribute('data-alpdt-config'));
      url = new URL(node.dataset.alpdtEndpoint, document.baseURI);
    } catch (e) { return; }
    if ((url.protocol !== 'https:' && url.protocol !== 'http:') || url.origin !== window.location.origin || url.pathname.indexOf('/alpdt/v1/render') === -1) return;
    if (!params || typeof params !== 'object' || Array.isArray(params)) return;
    Object.keys(params).forEach(function (key) { url.searchParams.set(key, params[key]); });
    fetch(url.href, { credentials: 'omit', cache: 'no-store' })
      .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
      .then(function (data) { if (typeof data.value === 'string') node.textContent = data.value; })
      .catch(function () { /* Retain server-side text on failure. */ });
  }
  Array.prototype.forEach.call(nodes,function(node){
    lastDays.set(node,siteDay(node.getAttribute('data-alpdt-zone')||'UTC'));
    refresh(node);
  });
  // One shared and low-frequency check, no polling requests until the site's calendar day changes.
  // Long-lived tabs remain correct after midnight, New Year and month boundaries.
  if(nodes.length)window.setInterval(function(){
    if(document.hidden)return;
    Array.prototype.forEach.call(nodes,function(node){
      if(!node.isConnected)return;
      var today=siteDay(node.getAttribute('data-alpdt-zone')||'UTC');
      if(lastDays.get(node)!==today){lastDays.set(node,today);refresh(node);}
    });
  },60000);
}());
