// on load create a script to load a final script here

$(function(){
  $("span.toggle > a").toggle(
    function () {
      $(this).text("Pokaż szczegóły");
      $("ul.feat").addClass("hidden");        
    },
    function () {
      $(this).text("Ukryj szczegóły");
      $("ul.feat").removeClass("hidden");
    }
  );
  
  $("#q").focus(function(){
    if($(this).text() == "Szukaj...") $(this).text("")
  });
  $("#q").focus();

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



});
