$(function() {
  $('#adm-edit-lyrics').click(function() {
    $('#adm-edit-lyrics').hide();
    var lyrics = $('#lyrics p').text();
    var songId = $('#id-song').text();
    $('#lyrics p').html('<div class="adm" id="adm-lyrics"><form action="/" method="post"><textarea name="adm-lyrics" rows="30">' + lyrics + '</textarea><input class="adm-save" type="submit" value="Zapisz" /><input type="hidden" name="adm-song-id" value="' + songId + '" id="com_object_id"></form></div>');
    
    $('#adm-lyrics .adm-save').click(function() {
      // alert($('#adm-lyrics form'));
      var dataString = $('#adm-lyrics form').serialize();
      // alert(dataString);
      $.ajax({
        type: 'POST',
        url: '/admin-interface/song',
        dataType: 'json',
        data: dataString,
        success: function(data) {
          saveSuccess(data);
        },
        error: function() {
          alert('Problem z zapisaniem formularza, spróbuj za jakiś czas.');
        }
      });
      return false;
    });
    return false;
  });
})

function saveSuccess (data) {
  $('#lyrics p').html(data['adm-lyrics']).hide().fadeIn('slow');
}