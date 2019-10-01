$(function () {
    // Initialisation des tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Smooth scrolling
    $(document).on('click', 'a[href^="#"]', function () {
        var $target = $($(this).attr('href'));
        $('html, body').animate({
            scrollTop: $target.offset().top
        }, 400);
    });

    // Initialisation de TinyMCE
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#content',
            min_height: 300,
            menubar: false,
            branding: false,
            statusbar: false,
            plugins: [
                'autolink autoresize codesample link lists paste'
            ],
            toolbar: 'bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link blockquote codesample',
        });
    }
});