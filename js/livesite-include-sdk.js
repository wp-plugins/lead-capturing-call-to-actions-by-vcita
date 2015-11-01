window.liveSiteAsyncInit = function() {
    LiveSite.init({
      // Gets uid from wordpress stored vcita credentials
      id : ls_PHPVAR_livesite_sdk.ls_sdk_uid,
    //   activeEngage: false,
      ui: ls_PHPVAR_livesite_sdk.ls_sdk_show_livesite
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0],
        p = (('https:' == d.location.protocol) ? 'https://' : '//'),
        r = Math.floor(new Date().getTime() / 1000000);
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = p + "www.vcita.com/assets/livesite.js?" + r;
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'vcita-jssdk'));
