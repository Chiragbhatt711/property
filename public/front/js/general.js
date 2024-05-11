(() => {
  'use strict'
  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')
  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()

$(document).ready(function() {
$('.review_slider').owlCarousel({
  margin:20,
  autoplay:true,
  loop: true, 
  nav:true,
  items:3,
  responsive:{
      0:{
          items:1
      },
      768:{
          items:2
      },
      992:{
          items:3
      }
  }
})
});

$(document).ready(function() {
  // var $nav_doc_left = $('#nav_doc ul');
  // var $nav_doc_top = $('#navbar-main ul');
  // var html_menu = '';


  // $('.code-example').each(function(ind, elem) {
  //   $('.code-example').hide()
  //   var html_example = $(elem).html();
  //   html_example = $.trim(html_example)
  //   var $prismContainer = $('<pre><code class="language-markup" id="code' + ind + '"></code></pre>');
  //   $prismContainer
  //     .find('code')
  //     .text(html_example)
  //   $(elem).after($prismContainer);

  //   $prismContainer.append('<button class="btn btn_copy" data-clipboard-target="#code' + ind + '" alt="Copy to clipboard" ><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></button>')

  // });

  // new Clipboard('.btn_copy');

  // $('.doc-section-title').each(function(ind, elem) {
  //   html_menu += '<li>';
  //   html_menu += '<a href="#' + $(elem).attr('id') + '">';
  //   html_menu += $(elem).text()
  //   html_menu += '</a>';
  //   html_menu += '</li>';
  // });

  // $nav_doc_left.html(html_menu)
  // $nav_doc_top.html(html_menu)


  // $('body').scrollspy({
  //   target: '#nav_doc'
  // });

  

});