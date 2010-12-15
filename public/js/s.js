// on load create a script to load a final script here



function getCookie (name) {
    var dc = document.cookie;
    var cname = name + "=";

    if (dc.length > 0) {
      begin = dc.indexOf(cname);
      if (begin != -1) {
        begin += cname.length;
        end = dc.indexOf(";", begin);
        if (end == -1) end = dc.length;
        return unescape(dc.substring(begin, end));
        }
      }
    return null;
}
// cookies handling
function setCookie(name, value, expires) {
  document.cookie = name + "=" + escape(value) + "; path=/" + ((expires == null) ? "" : "; expires=" + expires.toGMTString());
}



var exp = new Date();
exp.setTime(exp.getTime() + (1000 * 60 * 60 * 24 * 350));

$(function(){
  // toggle tracklist additional information
  $("#tracklist span.toggle > a").toggle(
    function () {
      $(this).text("Pokaż szczegóły");
      $("ul.feat").addClass("hidden");
      setCookie('albumShowDetails', 0, exp);   
    },
    function () {
      $(this).text("Ukryj szczegóły");
      $("ul.feat").removeClass("hidden");
      setCookie('albumShowDetails', 1, exp);
    }
  );
  
  // toggle view/hide autoDescription
  $("#description span.toggle > a").toggle(
    function () {
      $(this).text("Ukryj opis standardowy");
      $("p.auto").removeClass("hidden");
      setCookie('albumShowAuto', 1, exp);
    },
    function () {
      $(this).text("Pokaż opis standardowy");
      $("p.auto").addClass("hidden");
      setCookie('albumShowAuto', 0, exp);
    }
  );
  
  $("#q").focus(function(){
    if($(this).text() == "Szukaj...") $(this).text("")
  });
  // $("#q").focus();

  $('table tr').hover(
     function() {
      $(this).toggleClass('zebra');
     }
  );
  
  $('#rateUp').click(function() {
    $('#upCount').text(parseInt($('#upCount').text())+1);
  });

  $('#rateDown').click(function() {
    $('#downCount').text(parseInt($('#downCount').text())+1);
  });
  
  // unhide javascript functionality
  $('.jsHidden').show();
  

  if (getCookie('albumShowDetails') == 0) { $("#tracklist span.toggle > a").click(); };
  if (getCookie('albumShowAuto') == 1) { $("#description span.toggle > a").click(); };

  
});