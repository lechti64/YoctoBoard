// Initialisation de TinyMCE
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

