$(document).ready(function()
{
  $('.search input[type="submit"]').hide();
  $('#search_keywords').on('keyup', function()
  {
    if (this.value.length >=3 ||this.value =='') {
      $('#loader').show();
      $('#jobs').load(
        $(this).parent('form').attr('action'),
        { query: this.value ? thsi.value + '*' : this.value },
        function() {
          $('#loader').hide();
        }
      );
    }
  });
});