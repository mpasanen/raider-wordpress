(function(){
var c=window.raiderStats,d=document,n=navigator,L=location;
if(!c||n.webdriver||/bot|crawl|spider|lighthouse|headless|pagespeed|gtmetrix|ptst/i.test(n.userAgent)||d.body.classList.contains('logged-in'))return;
var id=0,key='',vis=0,last=d.visibilityState==='visible'?Date.now():0,sent=0;
function post(o,beacon){
var s=JSON.stringify(o);
if(beacon&&n.sendBeacon){n.sendBeacon(c.u,s);return null}
return fetch(c.u,{method:'POST',body:s,keepalive:true,credentials:'same-origin'});
}
function secs(){return Math.round((vis+(last?Date.now()-last:0))/1000)}
function flush(){var s=secs();if(id&&s>sent){sent=s;post({t:'d',i:id,k:key,s:s},true)}}
function start(){
var q=new URLSearchParams(L.search);
var r=post({t:'v',p:L.pathname,r:d.referrer,us:q.get('utm_source')||'',uc:q.get('utm_campaign')||'',w:innerWidth});
if(r)r.then(function(x){return x.status===200?x.json():null}).then(function(j){if(j&&j.i){id=j.i;key=j.k;[15,60,180,600].forEach(function(t){setTimeout(flush,t*1000)})}}).catch(function(){});
}
if(d.readyState==='complete')start();else addEventListener('load',start);
d.addEventListener('visibilitychange',function(){
if(d.visibilityState==='hidden'){if(last){vis+=Date.now()-last;last=0}flush()}else last=Date.now();
});
addEventListener('pagehide',flush);
d.addEventListener('click',function(e){
var a=e.target&&e.target.closest?e.target.closest('a[href]'):null;
if(!a)return;
var h=a.getAttribute('href'),t='',v='';
if(/^tel:/i.test(h)){t='phone';v=h.slice(4)}
else if(/^mailto:/i.test(h)){t='email';v=h.slice(7).split('?')[0]}
else if(/\/\/(wa\.me|api\.whatsapp\.com)\//i.test(h)){t='whatsapp';v=h.replace(/^.*\/\/[^/]+\//,'').split('?')[0]}
if(t)post({t:'e',n:t,v:v.slice(0,120),p:L.pathname},true);
},true);
})();
