$('.ckeditor').each(function() {
    ClassicEditor.create(document.querySelector('#' + $(this).attr('id')), {
            // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
        })
        .then(editor => {
            window.editor = editor;
        })
        .catch(err => {
            // console.error( err.stack );
        });
});
$('.icp-auto').iconpicker();
