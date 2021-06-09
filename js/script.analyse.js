// Coming soon
// Chart welche Blockzeit die meisten Reservierung hat
// Die 10 beliebtesten Tische
// Chart jeder Wochentag + Anzahl der Gäste
// Die meist gebuchtesten Events
// Welche Buttons werden wie oft angeklickt


$(document).on('click', '.analyse-zeit li', function(event){
  if(!$(this).hasClass('analyse-zeit-current')){
    $('.analyse-zeit li').removeClass('analyse-zeit-current');
    $(this).addClass('analyse-zeit-current');

    var time = $(this).text();
    window.location.href = 'admin.php?analyse='+time;
  }
});

$(document).on('click', '.analyse-zeit li input', function(event){
  if(!$(this).parent().hasClass('analyse-zeit-current')){
    $('.analyse-zeit li').removeClass('analyse-zeit-current');
    $(this).parent().addClass('analyse-zeit-current');
  }
});

$(document).on('change','#analyse-date',function(){
  var date = $(this).val();
  window.location.href = 'admin.php?analyse='+date;
});
